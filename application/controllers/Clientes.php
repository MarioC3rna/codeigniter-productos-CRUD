<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Controlador del CRUD de clientes (listar, crear, editar, eliminar)
class Clientes extends CI_Controller
{
    // Se ejecuta en cada petición antes que cualquier método
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Cliente_model');      // para hablar con la tabla clientes
        $this->load->library('form_validation');  // para validar los formularios
    }

    // Lista todos los clientes (página principal)
    public function index()
    {
        $data['clientes'] = $this->Cliente_model->obtener_todos();
        $this->load->view('templates/header', ['titulo' => 'Clientes']);
        $this->load->view('clientes/index', $data);
        $this->load->view('templates/footer');
    }

    // Crea un cliente: muestra el formulario o guarda si llegan datos por POST
    public function crear()
    {
        if ($this->input->method() === 'post') {


        
            // Reglas de validación de cada campo del formulario
            $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
            $this->form_validation->set_rules('apellido', 'Apellido', 'required|max_length[100]');
            $this->form_validation->set_rules('direccion', 'Dirección', 'max_length[200]');
            $this->form_validation->set_rules('telefono', 'Teléfono', 'numeric|max_length[20]');
            $this->form_validation->set_rules('email', 'Email', 'valid_email|max_length[100]');

            // Si todo pasa la validación, se guarda en la BD
            if ($this->form_validation->run()) {
                $this->Cliente_model->crear([
                         'nombre'    => $this->input->post('nombre'),
                         'apellido'  => $this->input->post('apellido'),
                         'direccion' => $this->input->post('direccion'),
                         'telefono'  => $this->input->post('telefono'),
                         'email'     => $this->input->post('email'),
                ]);


                // Mensaje que se verá en la página a la que redirigimos
                $this->session->set_flashdata('mensaje', 'Cliente creado correctamente.');
                redirect('clientes');
                return;
            }
        }

        // Si es GET o la validación falló, se muestra el formulario
        $this->load->view('templates/header', ['titulo' => 'Nuevo cliente']);
        $this->load->view('clientes/crear');
        $this->load->view('templates/footer');
    }




    // Edita un cliente según su id (ej: index.php/clientes/editar/3)
    public function editar($id)
    {
        $cliente = $this->Cliente_model->obtener($id);

        // Si el id no existe en la BD, muestra página 404
        if (!$cliente) {
            show_404();
            return;
        }

        if ($this->input->method() === 'post') {
                $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
                $this->form_validation->set_rules('apellido', 'Apellido', 'required|max_length[100]');
                $this->form_validation->set_rules('direccion', 'Dirección', 'max_length[200]');
                $this->form_validation->set_rules('telefono', 'Teléfono', 'numeric|max_length[20]');
                $this->form_validation->set_rules('email', 'Email', 'valid_email|max_length[100]');


            // Validación correcta: se actualiza y volvemos a la lista
            if ($this->form_validation->run()) {
                $this->Cliente_model->actualizar($id, [
                        'nombre'    => $this->input->post('nombre'),
                         'apellido'  => $this->input->post('apellido'),
                        'direccion' => $this->input->post('direccion'),
                        'telefono'  => $this->input->post('telefono'),
                         'email'     => $this->input->post('email'),
                        ]);

                        $this->session->set_flashdata('mensaje', 'Cliente actualizado correctamente.');
                        redirect('clientes');
                        return;
              
            }

            // Validación fallida: repuebla el formulario con lo que escribió el usuario,
            // no con los datos viejos de la BD
            foreach (['nombre', 'apellido', 'direccion', 'telefono', 'email'] as $campo) {
                $cliente->$campo = $this->input->post($campo);
                                                    }

    
        }

        // Se llega aquí por GET o cuando la validación falló
            $this->load->view('templates/header', ['titulo' => 'Editar cliente']);
            $this->load->view('clientes/editar', ['cliente' => $cliente]);
            $this->load->view('templates/footer');

        
    }

    // Elimina un cliente por su id y vuelve a la lista
    public function eliminar($id)
    {
        $this->Cliente_model->eliminar($id);
        $this->session->set_flashdata('mensaje', 'Cliente eliminado correctamente.');
        redirect('clientes');
    }
}
