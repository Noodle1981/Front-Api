<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class BuscadorClientes extends Component
{
    use WithPagination;
    
    public $search = '';
    public $status = '';
    public $perPage = 10;
    
    // Actualizar búsqueda resetea la paginación
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Client::query()
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('company', 'like', '%' . $this->search . '%')
                          ->orWhere('fantasy_name', 'like', '%' . $this->search . '%')
                          ->orWhere('cuit', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status !== '', function($q) {
                $q->where('is_active', $this->status === 'active');
            })
            ->orderBy('created_at', 'desc');
        
        return view('livewire.buscador-clientes', [
            'clients' => $query->paginate($this->perPage),
            'totalClients' => Client::count(),
            'activeClients' => Client::where('is_active', true)->count(),
        ]);
    }
}
