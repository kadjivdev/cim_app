@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>VENTES</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Acceuil</a></li>
                        <li class="breadcrumb-item active">Listes des ventes</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        @if($message = session('message'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Alert!</h5>
                            {{ $message }}
                        </div>
                        @endif

                        @if($message = session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Alert!</h5>
                            {{ $message }}
                        </div>
                        @endif
                        
                        <div class="card-header">
                            <h3 class="card-title"></h3>
                            @if(Auth::user()->roles()->where('libelle', 'VENDEUR')->exists() == true)
                            <a class="btn btn-success btn-sm" href="{{route('ventes.create')}}">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                                Vente
                            </a>
                            @endif

                            <form action="{{ route('ventes.index') }}" method="GET">
                                <div class="row">
                                    <div class="col-1"></div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="">Date début</label>
                                            <input type="date" class="form-control" name="debut" value="{{old('debut')}}" required>
                                        </div>
                                        @error('debut')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="">Date Fin</label>
                                            <input type="date" class="form-control" name="fin" value="{{old('fin')}}" required>
                                        </div>
                                        @error('fin')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="col-2">
                                        <button class="btn btn-primary" type="submit" style="margin-top: 2em">Afficher</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 12px">
                                <thead class="text-white text-center bg-gradient-gray-dark">
                                    <tr>
                                        <th>Code</th>
                                        @if(Auth::user()->roles()->where('libelle', 'SUPERVISEUR')->exists() == true)
                                        <th>Code Commande</th>
                                        @endif
                                        <th>BorLiv</th>
                                        <th>Date</th>
                                        <th>Client</th>
                                        <th>PU</th>
                                        <th>Qté</th>
                                        <th>Transport</th>
                                        <th>Remise</th>
                                        <th>Montant</th>
                                        @if(Auth::user()->roles()->where('libelle', 'VENDEUR')->exists() == true)
                                        <th>Zone</th>
                                        @endif
                                        <th>Statut</th>
                                        <th>Comptabilité</th>
                                        <th>Utilisateur</th>
                                        <th>Actualisation</th>
                                        @if(Auth::user()->roles()->where('libelle', 'VENDEUR')->exists() == true)
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="table-body">
                                    @if ($ventes->count() > 0)
                                    <?php $compteur = 1; ?>
                                    <?php $TotalMontant = 0; ?>
                                    <?php $TotalQuantite = 0; ?>
                                    @foreach($ventes as $vente)
                                    
                                    <?php $TotalQuantite = $TotalQuantite + $vente->qteTotal; ?>
                                    <?php $TotalMontant = $TotalMontant + $vente->montant; ?>

                                    <tr>
                                        <td>{{ $vente->code }}</td>
                                        @if(Auth::user()->roles()->where('libelle', 'SUPERVISEUR')->exists() == true)
                                        <td>{{!$vente->commandeclient->byvente ?  $vente->commandeclient->code : '---' }}</td>
                                        @endif
                                        <td class="text-center">
                                            @foreach ( $vente->vendus as $vendu )
                                            <b>{{ $vendu->programmation->bl_gest ? $vendu->programmation->bl_gest : $vendu->programmation->bl }}</b> <br>
                                            <span class="badge bg-dark">{{ $vendu->programmation->bl_gest?$vendu->programmation->bl_gest:'--'}} / {{$vendu->programmation->bl?$vendu->programmation->bl:'--'}}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">{{ date('d/m/Y', strtotime($vente->date)) }}</td>
                                        <td class="pl-2">
                                            {{ $vente->commandeclient->client->raisonSociale }}
                                        </td>
                                        <td class="text-right pr-3 pu">{{ number_format($vente->pu,0,","," ") }}</td>
                                        <td class="text-right pr-3 qte">{{ number_format($vente->qteTotal,0,","," ") }}</td>
                                        <td class="text-right pr-3 transport">{{ number_format($vente->transport,0,","," ") }}</td>
                                        <td class="text-right pr-3 remise">{{ number_format($vente->remise,0,","," ") }}</td>
                                        <td class="text-right pr-3 montant">{{ number_format($vente->montant,0,","," ") }}</td>
                                        @if(Auth::user()->roles()->where('libelle', 'VENDEUR')->exists() == true)
                                        <td class="pl-2"> {{ $vente->commandeclient->zone->libelle }} </td>
                                        @endif
                                        @if ($vente->statut == 'Vendue')
                                            @if(($vente->montant-$vente->remise) - $vente->reglements->sum('montant') == 0)
                                            <td class="text-center"><span class="badge badge-success">Soldé</span></td>
                                            @elseif( $vente->reglements->sum('montant') > 0)
                                            <td class="text-center"><span class="badge badge-warning">Solde en cours</span></td>
                                            @else
                                            <td class="text-center"><span class="badge badge-success">{{ $vente->statut }}</span></td>
                                            @endif
                                        @elseif ($vente->statut == 'Annulée')
                                            <td class="text-center"><span class="badge badge-danger">{{ $vente->statut }}</span></td>
                                        @else
                                            <td class="text-center"><span class="badge badge-info">{{ $vente->statut }}</span></td>
                                        @endif

                                        <!-- COMPTABILITE -->
                                        <td class="text-center">
                                            @if($vente->date_envoie_commercial)
                                            <span class="badge bg-success roundered">Envoyée</span>
                                            @else
                                            <span class="badge bg-dark roundered">Pas encore</span>
                                            @endif
                                        </td>
                                        <!-- END COMPTABILITE -->

                                        <td>{{ $vente->user->name }}</td>
                                        <td class="text-center">
                                            @if(Auth::user()->roles()->where('libelle', 'ADMINISTRATEUR')->exists() == true)
                                            @if( $vente->statut == 'Vendue')
                                            <a class="btn btn-danger btn-sm" href="{{ route('ventes.destroy', ['vente'=>$vente->id]) }}"><i class="fa-solid fa-trash-can"></i></a>
                                            @endif
                                            @endif
                                            @if(Auth::user()->roles()->where('libelle', 'ADMINISTRATEUR')->exists() || Auth::user()->roles()->where('libelle', 'VENDEUR')->exists())
                                                @if ($vente->statut == 'Vendue')
                                                <a class="btn btn-primary btn-sm" href="{{ route('ventes.show', ['vente'=>$vente->id]) }}"><i class="fa fa-print"></i></a>
                                                @if(false)
                                                <a class="btn btn-info btn-sm" href="{{ route('ventes.invalider', ['vente'=>$vente->id]) }}"><i class="fa-regular fa-rectangle-xmark"></i></a>
                                                @endif
                                            @elseif($vente->statut == 'Préparation')
                                                @if($vente->vendus->count() > 0)
                                                <a class="btn btn-success btn-sm" href="{{ route('vendus.create', ['vente'=>$vente->id]) }}" title="Valider"><i class="fa-solid fa-check"></i></a>
                                                @endif
                                                <a class="btn btn-secondary btn-sm" href="{{ route('vendus.create', ['vente'=>$vente->id]) }}" title="Ajouter Détails Vente"><i class="fa-solid fa-circle-plus"></i></a>
                                                <!--<a class="btn btn-warning btn-sm" href="{{ route('ventes.edit', ['vente'=>$vente->id, 'statuts'=>$vente->commandeclient->type_commande_id]) }}"><i class="fa-solid fa-pen-to-square"></i></a>-->
                                                <a class="btn btn-danger btn-sm" href="{{ route('ventes.delete', ['vente'=>$vente->id]) }}"><i class="fa-solid fa-trash-can"></i></a>
                                            @else

                                            @endif
                                            @endif
                                            <a class="btn btn-dark btn-sm" href="#" onclick="charger({{ $vente->id }} )" data-toggle="modal" data-target="#modal-lg"><i class="fa-solid fa-list"></i></a>
                                        </td>
                                        
                                        @if(Auth::user()->roles()->where('libelle', 'VENDEUR')->exists() == true)

                                        <td class="text-center">
                                            @if ($vente->date_envoie_commercial)
                                            <div class="dropdown">
                                                <button type="button" class="dropdown-toggle btn btn-success btn-sm" href="#" role="button" data-toggle="dropdown">
                                                    Actions<i class="dw dw-more"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-md-right dropdown-menu-icon-list drop text-sm">
                                                    <a class="dropdown-item" href="{{route('reglements.index',['vente'=>$vente])}}"><i class="fa-solid fa-file-invoice-dollar"></i> Règlement {{$vente->id}} <span class="badge badge-info">{{$vente->reglements ? count($vente->reglements):0}}</span></a>
                                                    @if($vente->type_vente_id == 2)
                                                    <a class="dropdown-item" href="{{route('echeances.index',['vente'=>$vente])}}"><i class="fa-solid fa-file-invoice-dollar"></i> Échéancier <span class="badge badge-info">{{$vente->echeances ? count($vente->echeances):0}}</span></a>
                                                    @endif

                                                    <small class="d-block"> <b>MODIFICATION</b> </small>
                                                    <!-- MODIFICATION -->
                                                    @if(!IsThisVenteUpdateDemandeOnceMade($vente) ||(!IsThisVenteUpdateDemandeAlreadyValidated($vente) && IsThisVenteUpdateDemandeAlreadyModified($vente)) )
                                                    <a class="dropdown-item bg-primary btn btn-sm" target="_blank" href="{{route('ventes.askUpdateVente',$vente->id)}}"><i class="bi bi-pencil-fill"></i>Demande de modification </a>
                                                    @elseif(IsThisVenteUpdateDemandeAlreadyValidated($vente))
                                                    <a class="dropdown-item bg-warning btn btn-sm" target="_blank" href="{{route('ventes.askUpdateVente',$vente->id)}}"><i class="bi bi-pencil-fill"></i>Modifier maintenant</a>
                                                    @else
                                                    <div class="text-center">
                                                        <span class="text-center bg-warning badge">En attente de validation</span>
                                                    </div>
                                                    @endif

                                                    <small class="d-block"> <b>SUPPRESSION</b> </small>
                                                    <!-- SUPPRESSION -->
                                                    @if(!IsThisVenteDeleteDemandeOnceMade($vente) || (!IsThisVenteDeleteDemandeAlreadyValidated($vente) && IsThisVenteDeleteDemandeAlreadyModified($vente)))
                                                    <a class="dropdown-item bg-warning btn btn-sm" target="_blank" href="{{route('ventes.askDeleteVente',$vente->id)}}"><i class="fa-solid fa-trash-can"></i>Demande de suppression </a>
                                                    @elseif(IsThisVenteDeleteDemandeAlreadyValidated($vente))
                                                    <a class="dropdown-item bg-danger btn btn-sm" target="_blank" href="{{route('ventes.delete',$vente->id)}}"><i class="fa-solid fa-trash-can"></i>Supprimer maintenant</a>
                                                    @else
                                                    <div class="text-center">
                                                        <span class="text-center bg-warning badge">En attente de validation</span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                                <tfoot class="text-white text-center bg-gradient-gray-dark">
                                    <tr>
                                        <th>Code</th>
                                        @if(Auth::user()->roles()->where('libelle', 'SUPERVISEUR')->exists() == true)
                                        <th>Code Commande</th>
                                        @endif
                                        <th>BorLiv</th>
                                        <th>Date</th>
                                        <th>Client</th>
                                        <th>PU</th>
                                        <th>Qté</th>
                                        <th>Transport</th>
                                        <th>Remise</th>
                                        <th>Montant</th>
                                        @if(Auth::user()->roles()->where('libelle', 'VENDEUR')->exists() == true)
                                        <th>Zone</th>
                                        @endif
                                        <th>Statut</th>
                                        <th>Comptabilité</th>
                                        <th>Utilisateur</th>
                                        <th>Actualisation</th>
                                        @if(Auth::user()->roles()->where('libelle', 'VENDEUR')->exists() == true)
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered table-sm">
                                        <tr>
                                            <br />
                                            <td class="" colspan="2"><b>Total Quantité Vendu</b></td>
                                            <td colspan="6" class="text-right"><b id='qte'>{{ number_format($TotalQuantite ?? 0,0,","," ") }} Tonnes</b></td>
                                        </tr>
                                        <tr>
                                            <br />
                                            <td class="" colspan="2"><b>Total Montant Vendu</b></td>
                                            <td colspan="6" class="text-right"><b id='montant'>{{ number_format($TotalMontant ?? 0,0,","," ")  }} FCFA</b></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if(!(Auth::user()->roles()->where('libelle', ['CONTROLEUR'])->exists() || Auth::user()->roles()->where('libelle', ['VALIDATEUR'])->exists() || Auth::user()->roles()->where('libelle', ['SUPERVISEUR'])->exists()))
                        <div class="card-footer text-center no-print">
                            <button class="btn btn-success" onclick="window.print()"><i class="fa fa-print"></i> Imprimer</button>
                        </div>
                        @endif
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white text-center bg-gradient-gray-dark">
                <h4 class="modal-title"> <em>Détail de la vente :</em> <b id="code"></b> </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Liste des camions ayant participé à la vente</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table class="table table-bordered" id="detailVente">
                            <thead>
                                <tr>
                                    <th style="width: 5px">#</th>
                                    <th>Code_BC</th>
                                    <th>Code_PR</th>
                                    <th>Immatriculations</th>
                                    <th>Chauffeurs (BL)</th>
                                    <th style="width: 10px">Qtité prelevée</th>
                                    <th>Destination</th>
                                    <th>Produit</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                </div>

                <button type="button" class="btn btn-default" data-dismiss="modal">Retour</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

@endsection
@section('script')
<script>
    function charger(id) {
        axios.get("{{env('APP_BASE_URL')}}ventes/detailVente/" + id).then((response) => {
            var ventes = response.data

            console.log(ventes);

            $('#detailVente tbody').empty();
            $.each(ventes, function(i, data) {
                var newRow = $('<tr><td class="text-center">' + (i + 1) + '</td><td class="text-center">' + data.codeBC + '</td><td class="text-center">' + data.code + '</td><td class="text-center">' + data.immatriculationTracteur + '</td><td class="text-center">' + data.nom + ' ' + data.prenom + ' <b>( ' + data.bl + ' )</b> ' + '</td><td class="text-center">' + data.qteVendu + '</td><td class="text-center">' + data.destination + '</td></td><td class="text-center">' + data.libelle + '</td></tr>');
                $('#detailVente').append(newRow);
                $('#code').text(data.vente);
            });
        }).catch(() => {
            console.error("Erreur");
        })
    };

    $('body').on('change', function() {
        const tableBody = document.querySelector('.table-body');
        let sumQte = 0;
        tableBody.querySelectorAll('tr').forEach(row => {
            const puCells = row.querySelectorAll('.qte');
            let puSum = 0;
            puCells.forEach(puCell => {
                puSum = puSum + parseFloat(puCell.textContent);
            });
            sumQte = sumQte + puSum;
        });
        $('#qte').text(sumQte + ' Tonnes')

        let sumMontant = 0;
        tableBody.querySelectorAll('tr').forEach(row => {
            const puCells = row.querySelectorAll('.montant');
            let puSum = 0;
            puCells.forEach(puCell => {
                puSum = puSum + parseFloat((puCell.textContent.replace(/ /g, "")));
            });
            sumMontant = sumMontant + puSum;
            $('#montant').text(sumMontant + ' FCFA')
        });

    });

    $(function() {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["excel", "pdf", "print"],
            "order": [
                [0, 'desc']
            ],
            "pageLength": 15,
            "columnDefs": [{
                    "targets": 8,
                    "orderable": false
                },
                {
                    "targets": 9,
                    "orderable": false
                }

            ],
            language: {
                "emptyTable": "Aucune donnée disponible dans le tableau",
                "lengthMenu": "Afficher _MENU_ éléments",
                "loadingRecords": "Chargement...",
                "processing": "Traitement...",
                "zeroRecords": "Aucun élément correspondant trouvé",
                "paginate": {
                    "first": "Premier",
                    "last": "Dernier",
                    "previous": "Précédent",
                    "next": "Suiv"
                },
                "aria": {
                    "sortAscending": ": activer pour trier la colonne par ordre croissant",
                    "sortDescending": ": activer pour trier la colonne par ordre décroissant"
                },
                "select": {
                    "rows": {
                        "_": "%d lignes sélectionnées",
                        "1": "1 ligne sélectionnée"
                    },
                    "cells": {
                        "1": "1 cellule sélectionnée",
                        "_": "%d cellules sélectionnées"
                    },
                    "columns": {
                        "1": "1 colonne sélectionnée",
                        "_": "%d colonnes sélectionnées"
                    }
                },
                "autoFill": {
                    "cancel": "Annuler",
                    "fill": "Remplir toutes les cellules avec <i>%d<\/i>",
                    "fillHorizontal": "Remplir les cellules horizontalement",
                    "fillVertical": "Remplir les cellules verticalement"
                },
                "searchBuilder": {
                    "conditions": {
                        "date": {
                            "after": "Après le",
                            "before": "Avant le",
                            "between": "Entre",
                            "empty": "Vide",
                            "equals": "Egal à",
                            "not": "Différent de",
                            "notBetween": "Pas entre",
                            "notEmpty": "Non vide"
                        },
                        "number": {
                            "between": "Entre",
                            "empty": "Vide",
                            "equals": "Egal à",
                            "gt": "Supérieur à",
                            "gte": "Supérieur ou égal à",
                            "lt": "Inférieur à",
                            "lte": "Inférieur ou égal à",
                            "not": "Différent de",
                            "notBetween": "Pas entre",
                            "notEmpty": "Non vide"
                        },
                        "string": {
                            "contains": "Contient",
                            "empty": "Vide",
                            "endsWith": "Se termine par",
                            "equals": "Egal à",
                            "not": "Différent de",
                            "notEmpty": "Non vide",
                            "startsWith": "Commence par"
                        },
                        "array": {
                            "equals": "Egal à",
                            "empty": "Vide",
                            "contains": "Contient",
                            "not": "Différent de",
                            "notEmpty": "Non vide",
                            "without": "Sans"
                        }
                    },
                    "add": "Ajouter une condition",
                    "button": {
                        "0": "Recherche avancée",
                        "_": "Recherche avancée (%d)"
                    },
                    "clearAll": "Effacer tout",
                    "condition": "Condition",
                    "data": "Donnée",
                    "deleteTitle": "Supprimer la règle de filtrage",
                    "logicAnd": "Et",
                    "logicOr": "Ou",
                    "title": {
                        "0": "Recherche avancée",
                        "_": "Recherche avancée (%d)"
                    },
                    "value": "Valeur"
                },
                "searchPanes": {
                    "clearMessage": "Effacer tout",
                    "count": "{total}",
                    "title": "Filtres actifs - %d",
                    "collapse": {
                        "0": "Volet de recherche",
                        "_": "Volet de recherche (%d)"
                    },
                    "countFiltered": "{shown} ({total})",
                    "emptyPanes": "Pas de volet de recherche",
                    "loadMessage": "Chargement du volet de recherche..."
                },
                "buttons": {
                    "copyKeys": "Appuyer sur ctrl ou u2318 + C pour copier les données du tableau dans votre presse-papier.",
                    "collection": "Collection",
                    "colvis": "Visibilité colonnes",
                    "colvisRestore": "Rétablir visibilité",
                    "copy": "Copier",
                    "copySuccess": {
                        "1": "1 ligne copiée dans le presse-papier",
                        "_": "%ds lignes copiées dans le presse-papier"
                    },
                    "copyTitle": "Copier dans le presse-papier",
                    "csv": "CSV",
                    "excel": "Excel",
                    "pageLength": {
                        "-1": "Afficher toutes les lignes",
                        "_": "Afficher %d lignes"
                    },
                    "pdf": "PDF",
                    "print": "Imprimer"
                },
                "decimal": ",",
                "info": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                "infoEmpty": "Affichage de 0 à 0 sur 0 éléments",
                "infoThousands": ".",
                "search": "Rechercher:",
                "thousands": ".",
                "infoFiltered": "(filtrés depuis un total de _MAX_ éléments)",
                "datetime": {
                    "previous": "Précédent",
                    "next": "Suivant",
                    "hours": "Heures",
                    "minutes": "Minutes",
                    "seconds": "Secondes",
                    "unknown": "-",
                    "amPm": [
                        "am",
                        "pm"
                    ],
                    "months": [
                        "Janvier",
                        "Fevrier",
                        "Mars",
                        "Avril",
                        "Mai",
                        "Juin",
                        "Juillet",
                        "Aout",
                        "Septembre",
                        "Octobre",
                        "Novembre",
                        "Decembre"
                    ],
                    "weekdays": [
                        "Dim",
                        "Lun",
                        "Mar",
                        "Mer",
                        "Jeu",
                        "Ven",
                        "Sam"
                    ]
                },
                "editor": {
                    "close": "Fermer",
                    "create": {
                        "button": "Nouveaux",
                        "title": "Créer une nouvelle entrée",
                        "submit": "Envoyer"
                    },
                    "edit": {
                        "button": "Editer",
                        "title": "Editer Entrée",
                        "submit": "Modifier"
                    },
                    "remove": {
                        "button": "Supprimer",
                        "title": "Supprimer",
                        "submit": "Supprimer",
                        "confirm": {
                            "1": "etes-vous sure de vouloir supprimer 1 ligne?",
                            "_": "etes-vous sure de vouloir supprimer %d lignes?"
                        }
                    },
                    "error": {
                        "system": "Une erreur système s'est produite"
                    },
                    "multi": {
                        "title": "Valeurs Multiples",
                        "restore": "Rétablir Modification",
                        "noMulti": "Ce champ peut être édité individuellement, mais ne fait pas partie d'un groupe. ",
                        "info": "Les éléments sélectionnés contiennent différentes valeurs pour ce champ. Pour  modifier et "
                    }
                }
            },



        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

@endsection