<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Contraseñas con bcrypt (password_hash) en lugar del hash_hmac sha256 con clave fija.
 *
 * Los hashes viejos (hash_hmac) siguen funcionando: al ingresar con uno de ellos se reemplaza
 * por bcrypt. Se exceptua la contraseña por defecto del sistema (configuracion.passDefecto),
 * que queda en el formato viejo para que admin/ajax/cambiarPassDefecto pueda encontrar
 * a los usuarios que aun no la cambiaron (esa contraseña la conoce el administrador).
 */
class Auth_ORM extends Kohana_Auth_ORM {

    /** Hash nuevo para guardar en users.password (60 caracteres). */
    public function crear_hash($password)
    {
        return password_hash((string) $password, PASSWORD_BCRYPT);
    }

    /** Hash en el formato viejo; solo para la contraseña por defecto. */
    public function hash_legado($password)
    {
        return $this->hash((string) $password);
    }

    /** Compara la contraseña con el hash guardado, sea bcrypt o del formato viejo. */
    public function verificar($password, $guardado)
    {
        $password = (string) $password;
        $guardado = (string) $guardado;
        if ($password === '' || $guardado === '') {
            return FALSE;
        }
        if ($this->es_bcrypt($guardado)) {
            return password_verify($password, $guardado);
        }
        // el formulario de ingreso antes enviaba la contraseña pasada por HTML::chars:
        // los hashes viejos pueden estar hechos con cualquiera de las dos formas
        return hash_equals($guardado, $this->hash($password))
            || hash_equals($guardado, $this->hash(HTML::chars($password)));
    }

    public function es_bcrypt($guardado)
    {
        return strncmp((string) $guardado, '$2y$', 4) === 0;
    }

    /** La contraseña recibida es la contraseña por defecto vigente del sistema. */
    public function es_pass_defecto($password)
    {
        $config = ORM::factory('configuracion')->where('campo', '=', 'passDefecto')->find();
        return $config->campo === 'passDefecto' && (string) $config->valor !== '' && (string) $config->valor === (string) $password;
    }

    // la contraseña llega en texto plano; se compara en _login
    public function login($username, $password, $remember = FALSE)
    {
        if (empty($password) || !is_string($password)) {
            return FALSE;
        }
        return $this->_login($username, $password, $remember);
    }

    protected function _login($user, $password, $remember)
    {
        if (!is_object($user)) {
            $username = $user;
            $user = ORM::factory('user');
            $user->where($user->unique_key($username), '=', $username)->find();
        }

        if (!$user->loaded() || !$user->has('roles', ORM::factory('role', array('name' => 'login')))) {
            return FALSE;
        }
        if (!$this->verificar($password, $user->password)) {
            return FALSE;
        }

        // hash viejo: se pasa a bcrypt (salvo la contraseña por defecto, ver arriba)
        if (!$this->es_bcrypt($user->password) && !$this->es_pass_defecto($password)) {
            DB::update('users')->set(array('password' => $this->crear_hash($password)))
                ->where('id', '=', $user->id)->execute();
            $user->reload();
        }

        if ($remember === TRUE) {
            $data = array(
                'user_id' => $user->id,
                'expires' => time() + $this->_config['lifetime'],
                'user_agent' => sha1(Request::$user_agent),
            );
            $token = ORM::factory('user_token')->values($data)->create();
            Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
        }

        $this->complete_login($user);
        return TRUE;
    }

    public function check_password($password)
    {
        $user = $this->get_user();
        return $user ? $this->verificar($password, $user->password) : FALSE;
    }

}
