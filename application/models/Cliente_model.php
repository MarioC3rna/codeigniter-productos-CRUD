<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Modelo de la tabla clientes: aquí van TODAS las consultas a la BD
class Cliente_model extends CI_Model
{
    // Nombre de la tabla con la que trabaja este modelo
    private $table = 'clientes';

    public function __construct()
    {
        parent::__construct();
    }

    // Devuelve todos los clientes ordenados por nombre (SELECT * FROM clientes)
    public function obtener_todos()
    {
        return $this->db->order_by('nombre', 'ASC')->get($this->table)->result();
    }

    // Busca UN cliente por su id (row() devuelve una sola fila)
    public function obtener($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    // Inserta un cliente nuevo (INSERT INTO clientes ...)
    public function crear($datos)
    {
        return $this->db->insert($this->table, $datos);
    }

    // Actualiza los campos del cliente con ese id (UPDATE WHERE id = ...)
    public function actualizar($id, $datos)
    {
        return $this->db->where('id', $id)->update($this->table, $datos);
    }

    // Elimina el cliente con ese id (DELETE WHERE id = ...)
    public function eliminar($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }
}
