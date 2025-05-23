@extends('layouts.app')



@section('content')

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>ZONES</h1>

                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item"><a href="#">Acceuil</a></li>

                        <li class="breadcrumb-item active">Listes zones</li>

                    </ol>

                </div>

            </div>

        </div><!-- /.container-fluid -->

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

                            <h5><i class="fa-solid fa-circle-exclamation"></i> Erreur!</h5>

                            {{ $message }}

                        </div>

                        @endif
                        <div class="card-header">

                            <h3 class="card-title"></h3>

                            <a class="btn btn-success btn-sm" href="{{route('zones.create')}}">

                                <i class="fas fa-solid fa-plus"></i>

                                Ajouter

                            </a>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">

                            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 12px">

                                <thead class="text-white text-center bg-gradient-gray-dark">

                                    <tr>
                                        <th>Libelle</th>

                                        <th>Représentant</th>

                                        <!--  -->
                                        <th>Stock</th>
                                        <th>Détail du stock</th>
                                        <!--  -->

                                        <th>Département</th>

                                        @if(!(Auth::user()->roles()->where('libelle', ['CONTROLEUR'])->exists() || Auth::user()->roles()->where('libelle', ['VALIDATEUR'])->exists() || Auth::user()->roles()->where('libelle', ['SUPERVISEUR'])->exists()))
                                        <th>Actualisation</th>
                                        <th>Action</th>
                                        @endif
                                    </tr>

                                </thead>

                                <tbody>

                                    @if ($zones->count() > 0)

                                    @foreach($zones as $zone)

                                    <?php
                                    $programmations = $bon_programmations->where("zone_id", $zone->id);
                                    $qteProg = $programmations->sum("qteprogrammer");
                                  
                                    $proVendus = [];
                                    foreach ($programmations as $prog) {
                                        array_push($proVendus, $prog->vendus->sum("qteVendu"));
                                    }

                                    $qteVendus = array_sum($proVendus);

                                    //Calcul du stock
                                    $stock = $qteProg - $qteVendus;

                                    //
                                    $_qteProg = [];
                                    $_qteVendue = [];
                                    $_stock = [];
                                    ?>

                                    <tr>
                                        <td>{{ $zone->libelle }}</td>

                                        <td>@if($zone->representant){{ $zone->representant->civilite }} {{ $zone->representant->nom }} {{ $zone->representant->prenom }}@endif</td>

                                        <td class="text-center"><span class="badge bg-danger">{{number_format($stock,2,'.',' ')}} </span> </td>
                                        <td class="text-left" style="width:auto;">
                                            <div style="width:auto;height:100px!important;overflow-y: scroll">
                                                @foreach($programmations as $programmation)
                                                <?php
                                                $reste = $programmation->qteprogrammer - $programmation->vendus->sum("qteVendu");
                                                array_push($_stock, $reste);
                                                array_push($_qteProg, $programmation->qteprogrammer);
                                                array_push($_qteVendue, $programmation->vendus->sum("qteVendu"));; ?>
                                                @if($programmation->qteprogrammer>$programmation->vendus->sum("qteVendu"))
                                                @if($programmation->zone)
                                                <span class="badge d-block bg-dark">{{$programmation->zone->_user?->name}} (blguest/bl : {{$programmation->bl_gest}}/{{$programmation->bl}} ; Reste : {{$reste}})</span>
                                                <span class="badge d-block bg-dark">Qte Prog: {{$programmation->qteprogrammer}} ; QteVendue : {{$programmation->vendus->sum("qteVendu")}}</span>
                                                <hr>
                                                @endif
                                                @endif
                                                @endforeach
                                            </div>
                                        </td>

                                        <td>@if($zone->departement){{ $zone->departement->libelle }}@endif</td>

                                        @if(!(Auth::user()->roles()->where('libelle', ['CONTROLEUR'])->exists() || Auth::user()->roles()->where('libelle', ['VALIDATEUR'])->exists() || Auth::user()->roles()->where('libelle', ['SUPERVISEUR'])->exists()))
                                        <td class="text-center">
                                            <a class="btn btn-warning btn-sm" href="{{ route('zones.edit', ['zone'=>$zone->id]) }}"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a class="btn btn-danger btn-sm" href="{{ route('zones.delete', ['zone'=>$zone->id]) }}"><i class="fa-regular fa-money-bill-1"></i></a>
                                        </td>

                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button type="button" class="dropdown-toggle btn btn-success btn-sm" href="#" role="button" data-toggle="dropdown">
                                                    Actions<i class="dw dw-more"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-md-right dropdown-menu-icon-list drop text-sm">
                                                    <a class="dropdown-item" href="#"><i class="fa-solid fa-list-check"></i> Programmation</a>
                                                    <a class="dropdown-item" href="#"><i class="fa-solid fa-list-check"></i> Prix </a>
                                                </div>
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>

                                <tfoot class="text-white text-center bg-gradient-gray-dark">
                                    <tr>
                                        <th>Libelle</th>
                                        <th>Représentant</th>
                                        <!--  -->
                                        <th>Stock</th>
                                        <th>Détail du stock</th>
                                        <!--  -->

                                        <th>Département</th>
                                        @if(!(Auth::user()->roles()->where('libelle', ['CONTROLEUR'])->exists() || Auth::user()->roles()->where('libelle', ['VALIDATEUR'])->exists() || Auth::user()->roles()->where('libelle', ['SUPERVISEUR'])->exists()))
                                        <th>Actualisation</th>

                                        <th>Action</th>
                                        @endif
                                    </tr>

                                </tfoot>

                            </table>

                        </div>

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

@endsection

@section('script')

<script>
    $(function() {

        $("#example1").DataTable({

            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,

            "buttons": ["pdf", "print"],

            "order": [
                [1, 'asc']
            ],

            "pageLength": 15,

            "columnDefs": [





                {

                    "targets": 2,

                    "orderable": false

                },

                {

                    "targets": 3,

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