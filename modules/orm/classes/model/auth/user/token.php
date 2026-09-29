<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Default auth user toke
 *
 * @package    Kohana/Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Model_Auth_User_Token extends ORM {

       protected $_table_name='user_token';
    
	// Relationships
	protected $_belongs_to = array('user' => array());

	/**
	 * Handles garbage collection and deleting of expired objects.
	 *
	 * @return  void
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		if (mt_rand(1, 100) === 1)
		{
			// Do garbage collection
			$this->delete_expired();
		}

		if ($this->expires < time() AND $this->_loaded)
		{
			// This object has expired
			$this->delete();
		}
	}

	/**
	 * Deletes all expired tokens.
	 *
	 * @return  ORM
	 */
	public function delete_expired()
	{
		// Delete all expired tokens
		DB::delete($this->_table_name)
			->where('expires', '<', time())
			->execute($this->_db);

		return $this;
	}

	public function create(Validation $validation = NULL)
	{
		$this->token = $this->create_token();

		// Con MySQL en modo estricto, las columnas NOT NULL sin valor por defecto hacen fallar el INSERT
		// (error 1364 "Field 'type' doesn't have a default value" al ingresar con "Recordar").
		if (array_key_exists('created', $this->_table_columns) AND ! $this->created)
		{
			$this->created = time();
		}
		if (array_key_exists('type', $this->_table_columns) AND $this->type === NULL)
		{
			$this->type = '';
		}

		return parent::create($validation);
	}

	protected function create_token()
	{
		do
		{
			$token = sha1(uniqid(Text::random('alnum', 32), TRUE));
		}
		while(ORM::factory('usertoken', array('token' => $token))->loaded());

		return $token;
	}

} // End Auth User Token Model