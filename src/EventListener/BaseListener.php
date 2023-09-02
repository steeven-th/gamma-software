<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class BaseListener {

    private EntityManagerInterface $em;
    private Environment            $twig;

    public function __construct(EntityManagerInterface $em,
                                Environment            $twig) {

        $this->em = $em; // Récupération du service d'accès à la base de données
        $this->twig = $twig; // Récupération du service de rendu de vues

        $this->twig->addGlobal("websiteroot",
                               (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST']);
    }

    // Cette méthode est appelée à chaque requête
    public function onKernelRequest(RequestEvent $event): void {
    }
}