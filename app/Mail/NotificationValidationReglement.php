<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class NotificationValidationReglement extends Mailable
{
    use Queueable, SerializesModels;
    private $user;
    private $vente;
    private $message_html;
    private $destataire;
    private $copies;
    private $objet;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($destataire,$objet,$message_html,$vente,$copies=null)
    {
        $this->$vente = $vente;
        $this->user = Auth::user();
        $this->message_html = $message_html;
        $this->copies = $copies;
        $this->destataire = $destataire;
        $this->objet = $objet;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->destataire['email'])
            ->subject($this->objet)
            ->cc($this->copies ?:[])
            ->view('Email.validationReglement')
            ->with([
                'user'=>$this->user,
                'vente'=>$this->vente,
                'destinataire'=>$this->destataire,
                'message_html'=>$this->message_html,
                'copies'=>$this->copies,
                'statut'=> 0
            ]);
    }
}
