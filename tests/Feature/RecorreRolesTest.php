<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecorreRolesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('local');
        config()->set('recorre.admin_email', 'admin@example.test');
    }

    private function usuario(int $id, string $email): User
    {
        $usuario = new User([
            'name' => 'Usuario de prueba',
            'email' => $email,
            'password' => 'password',
        ]);
        $usuario->id = $id;

        return $usuario;
    }

    private function datosEstablecimiento(string $nombre = 'Vinoteca de prueba'): array
    {
        return [
            'nombre' => $nombre,
            'tipo' => 'vinoteca',
            'direccion' => 'Calle de prueba 123',
            'ciudad' => 'Santa Fe',
            'provincia' => 'Santa Fe',
            'latitud' => -31.6333,
            'longitud' => -60.7000,
        ];
    }

    private function registros(string $archivo): array
    {
        return json_decode(Storage::disk('local')->get('recorre/'.$archivo), true);
    }

    public function test_un_socio_solo_puede_solicitar_alta_pendiente_y_no_puede_aprobarse(): void
    {
        $this->actingAs($this->usuario(2, 'vinoteca@example.test'));

        $this->post(route('recorre.registrar'), $this->datosEstablecimiento())
            ->assertRedirect(route('recorre.panel'));

        $alta = $this->registros('establecimientos.json')[0];
        $this->assertSame('pendiente', $alta['estado']);
        $this->assertSame(2, $alta['user_id']);
        $this->assertSame('vinoteca@example.test', $alta['correo_contacto']);

        $this->get(route('recorre.admin'))->assertForbidden();
        $this->post(route('recorre.admin.crear'), $this->datosEstablecimiento('Intento indebido'))
            ->assertForbidden();
        $this->post(route('recorre.moderar', ['establecimientos', $alta['id']]), [
            'estado' => 'aprobado',
        ])->assertForbidden();

        $this->get(route('recorre.index'))->assertDontSee('Vinoteca de prueba');
    }

    public function test_administrador_tiene_panel_exclusivo_y_puede_agregar_establecimiento_aprobado(): void
    {
        $this->actingAs($this->usuario(1, 'admin@example.test'));

        $this->get(route('recorre.panel'))->assertRedirect(route('recorre.admin'));
        $this->post(route('recorre.registrar'), $this->datosEstablecimiento())
            ->assertForbidden();
        $this->post(route('recorre.admin.crear'), $this->datosEstablecimiento('Vinoteca administrada'))
            ->assertRedirect(route('recorre.admin'));

        $alta = $this->registros('establecimientos.json')[0];
        $this->assertSame('aprobado', $alta['estado']);
        $this->assertNull($alta['user_id']);

        $this->get(route('recorre.admin'))->assertOk()->assertSee('Vinoteca administrada');
        $this->get(route('recorre.index'))->assertOk()->assertSee('Vinoteca administrada');
    }

    public function test_admin_aprueba_solicitud_ajena_y_eventos_se_moderan_por_separado(): void
    {
        $socio = $this->usuario(2, 'socio@example.test');
        $admin = $this->usuario(1, 'admin@example.test');

        $this->actingAs($socio)
            ->post(route('recorre.registrar'), $this->datosEstablecimiento())
            ->assertRedirect(route('recorre.panel'));

        $id = $this->registros('establecimientos.json')[0]['id'];

        $this->actingAs($admin)
            ->post(route('recorre.moderar', ['establecimientos', $id]), ['estado' => 'aprobado'])
            ->assertRedirect(route('recorre.admin'));

        $this->assertSame('aprobado', $this->registros('establecimientos.json')[0]['estado']);

        $this->actingAs($socio)
            ->post(route('recorre.eventos'), [
                'establecimiento_id' => $id,
                'titulo' => 'Cata de primavera',
                'fecha' => now()->addDays(3)->toDateString(),
            ])
            ->assertRedirect(route('recorre.panel'));

        $evento = $this->registros('eventos.json')[0];
        $this->assertSame('pendiente', $evento['estado']);
        $this->get(route('recorre.index'))->assertDontSee('Cata de primavera');

        $this->actingAs($admin)
            ->post(route('recorre.moderar', ['eventos', $evento['id']]), ['estado' => 'aprobado'])
            ->assertRedirect(route('recorre.admin'));
        $this->assertSame('aprobado', $this->registros('eventos.json')[0]['estado']);
    }

    public function test_admin_no_puede_autoaprobar_una_solicitud_antigua_de_su_propia_cuenta(): void
    {
        Storage::disk('local')->put('recorre/establecimientos.json', json_encode([[
            'id' => 'b5946c94-d72a-4c50-8f31-123456789abc',
            'user_id' => 1,
            'nombre' => 'Alta antigua del administrador',
            'estado' => 'pendiente',
        ]]));

        $this->actingAs($this->usuario(1, 'admin@example.test'))
            ->post(route('recorre.moderar', [
                'establecimientos', 'b5946c94-d72a-4c50-8f31-123456789abc',
            ]), ['estado' => 'aprobado'])
            ->assertForbidden();

        $this->assertSame('pendiente', $this->registros('establecimientos.json')[0]['estado']);
    }
}
