<?php

namespace App\Http\Controllers;

use App\Models\BonCommande;
use App\Models\CommandeClient;
use App\Models\Programmation;
use App\Models\Vente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {

        $user = Auth::user();
        // Eager load les rôles une seule fois
        $userRoles = $user->roles->pluck('libelle')->toArray();

        // Redirections selon le rôle
        if (array_intersect($userRoles, ['ADMINISTRATEUR', 'CONTROLEUR', 'VALIDATEUR', 'SUPERVISEUR'])) {
            if (in_array('CONTROLEUR VENTE', $userRoles)) {
                return redirect()->route('ventes.venteAEnvoyerComptabiliser');
            }
            if (array_intersect($userRoles, ['GESTIONNAIRE', 'COMPTABLE', 'VALIDATEUR'])) {
                return redirect()->route('boncommandes.index');
            }
            if (array_intersect($userRoles, ['VENDEUR', 'SUPERVISEUR'])) {
                return redirect()->route('livraisons.index');
            }
        }

        // Statistiques
        $boncommandesP = BonCommande::where('statut', 'Préparation')->count();
        $boncommandesV = BonCommande::where('statut', 'Valider')->count();
        $programmationsV = Programmation::where('statut', 'Valider')->count();
        $cdes = BonCommande::with('recus')->where('statut', 'Valider')->get();
        $_progs = Programmation::where('statut', 'Livrer')->get();
        $progs = $_progs->count();
        $qteLiv = $_progs->sum('qtelivrer');
        $sansRecu = 0;
        foreach ($cdes as $cde) {
            if ($cde->recus->isEmpty()) {
                $sansRecu++;
            }
        }
        $produitNP = $progs;
        $now = Carbon::now();
        $vente = Vente::where('statut', 'Vendue')
            ->whereBetween('date', [$now->copy()->startOfWeek()->format('Y-m-d'), $now->copy()->endOfWeek()->format('Y-m-d')])
            ->sum('montant');
        $cde = CommandeClient::where('statut', 'Valider')
            ->whereBetween('dateBon', [$now->copy()->startOfWeek()->format('Y-m-d'), $now->copy()->endOfWeek()->format('Y-m-d')])
            ->count();
        $impayer = 0;
        $umpaid_vente = 0;
        $ventes = Vente::with('reglements')
            ->where('statut', 'Vendue')
            ->where('type_vente_id', 2)
            ->orderByDesc('code')->get();
        foreach ($ventes as $vte) {
            $reglementsSum = $vte->reglements->sum('montant');
            if (($vte->montant - $reglementsSum) != 0) {
                $umpaid_vente++;
                $impayer += $vte->montant - $reglementsSum;
            }
        }
        // Variables inutilisées supprimées : $nbrLiv, $client
        return view('dashboard', compact('boncommandesP', 'boncommandesV', 'programmationsV', 'produitNP', 'sansRecu', 'qteLiv', 'vente', 'cde', 'impayer', 'umpaid_vente'));
    }
}
