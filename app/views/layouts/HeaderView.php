<?php

class HeaderView extends View
{
    public function render()
    {
        $this->renderDocType();
        $this->renderHead();
        $this->renderTopBar();
        $this->renderTitle();
        $this->renderNavigation();
        $this->renderFlashMessages();
    }
    
    private function renderDocType()
    {
        echo '<!DOCTYPE html>';
        echo '<html lang="fr">';
    }
    
    private function renderHead()
    {
        ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Melliti Abdelmalek">
    <meta name="description" content="Application Web pour le laboratoire LMCS de l'ESI d'Alger">
    <title><?php echo $this->escape($this->data['pageTitle'] ?? $this->pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>

<body class="bg-gradient-to-br from-blue-900 to-blue-700 min-h-screen">
    <?php
    }
    
    private function renderTopBar()
    {
        ?>
    <div class="bg-blue-950 text-white py-2">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <?php $this->renderLogo(); ?>
            <?php $this->renderTopBarLinks(); ?>
        </div>
    </div>
    <?php
    }
    
    private function renderLogo()
    {
        ?>
    <div class="flex items-center space-x-4">
        <img src="<?php echo ASSETS_URL; ?>images/LMCS.png" alt="Logo" class="h-12"
            onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2250%22 height=%2250%22%3E%3Crect width=%2250%22 height=%2250%22 fill=%22%23ddd%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23666%22%3ELOGO%3C/text%3E%3C/svg%3E'">
    </div>
    <?php
    }
    
    private function renderTopBarLinks()
    {
        ?>
    <div class="flex items-center space-x-6">
        <?php $this->renderSocialLinks(); ?>
        <a href="https://www.esi.dz/" class="hover:text-blue-300 transition text-sm">
            <img src="<?php echo ASSETS_URL; ?>images/ESI-Logo.png" alt="Logo" class="h-12"
                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2250%22 height=%2250%22%3E%3Crect width=%2250%22 height=%2250%22 fill=%22%23ddd%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23666%22%3ELOGO%3C/text%3E%3C/svg%3E'">
        </a>
        <?php $this->renderAuthSection(); ?>
    </div>
    <?php
    }
    
    private function renderSocialLinks()
    {
        $socialLinks = [
            'facebook' => [
                'path' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
                'url'  => 'https://www.facebook.com/ESI.Page/'
            ],
            'twitter'  => [
                'path' => 'M24 4.557a9.83 9.83 0 01-2.828.775 4.932 4.932 0 002.165-2.724c-.951.564-2.005.974-3.127 1.195a4.916 4.916 0 00-8.38 4.482A13.94 13.94 0 011.671 3.149a4.916 4.916 0 001.523 6.574 4.897 4.897 0 01-2.228-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.935 4.935 0 01-2.224.084 4.918 4.918 0 004.59 3.417A9.867 9.867 0 010 19.54a13.94 13.94 0 007.548 2.209c9.142 0 14.307-7.721 13.995-14.646A9.936 9.936 0 0024 4.557z',
                'url'  => 'https://x.com/EsiAlger'
            ],
            'linkedin' => [
                'path' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
                'url'  => 'https://www.linkedin.com/school/ecole-superieure-informatique-alger/posts/?feedView=all'
            ]
        ];

        foreach ($socialLinks as $platform => $data) {
            echo '<a href="' . $data['url'] . '" target="_blank" class="hover:text-blue-300 transition">';
            echo '<svg class="w-5 h-5 inline" fill="currentColor" viewBox="0 0 24 24">';
            echo '<path d="' . $data['path'] . '"></path>';
            echo '</svg>';
            echo '</a>';
        }
    }
    
    private function renderAuthSection()
    {
        if (isset($_SESSION['user_id'])) {
            ?>
    <div class="flex items-center space-x-4">
        <a href="<?php echo BASE_URL; ?>dashboard" class="flex items-center space-x-2 hover:opacity-80 transition">
            <?php 
            $user = [
                'photo' => $_SESSION['photo'] ?? null,
                'prenom' => $_SESSION['prenom'] ?? '',
                'nom' => $_SESSION['nom'] ?? ''
            ];
            ?>
            <div class="flex items-center space-x-2">
                <?php if (!empty($user['photo'])): ?>
                <img src="<?php echo ASSETS_URL . 'images/users/' . $user['photo']; ?>" alt="Photo de profil"
                    class="w-10 h-10 rounded-full object-cover border-2 border-blue-300"
                    onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23667eea%22/%3E%3C/svg%3E'">
                <?php else: ?>
                <div
                    class="w-10 h-10 rounded-full bg-blue-300 flex items-center justify-center text-blue-900 text-sm font-bold border-2 border-blue-400">
                    <?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?>
                </div>
                <?php endif; ?>
                <span class="text-sm font-medium">Mon espace</span>
            </div>
        </a>
        <a href="<?php echo BASE_URL; ?>auth/logout"
            class="bg-red-600 px-4 py-1 rounded hover:bg-red-700 transition text-sm">Déconnexion</a>
    </div>
    <?php
        } else {
            ?>
    <a href="<?php echo BASE_URL; ?>auth/login"
        class="bg-blue-600 px-4 py-1 rounded hover:bg-blue-700 transition text-sm">Se connecter</a>
    <?php
        }
    }
    
    private function renderTitle()
    {
        ?>
    <div class="bg-gradient-to-r from-blue-800 to-blue-900 py-6 shadow-lg">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white tracking-wide">
                Laboratoire de Méthodes de Conception des Systèmes
            </h1>
        </div>
    </div>
    <?php
    }

    private function renderNavigation()
    {
        ?>
    <nav class="bg-white shadow-lg sticky top-0 z-10">
        <div class="container mx-auto px-4">
            <ul class="flex space-x-8 py-4">
                <?php $this->renderMenuItems(); ?>
            </ul>
        </div>
    </nav>
    <?php
    }
    
    private function renderMenuItems()
    {
        $menuItems = [
            '' => 'Accueil',
            'projet' => 'Projets',
            'publication' => 'Publications',
            'equipement' => 'Équipements',
            'team' => 'Membres',
            'event' => 'Événements',
            'offer' => 'Opportunités',
            'contact' => 'Contact'
        ];
        
        foreach ($menuItems as $url => $label) {
            echo '<li><a href="' . BASE_URL . $url . '" class="text-gray-700 hover:text-blue-600 font-semibold transition">' . $label . '</a></li>';
        }
    }
    
    private function renderFlashMessages()
    {
        $hasSuccess = isset($_SESSION['success']);
        $hasError = isset($_SESSION['error']);
        
        if ($hasSuccess || $hasError) {
            ?>
    <div id="flash-container" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 space-y-4 w-full max-w-md px-4">
        <?php
            if ($hasSuccess) {
                $this->renderFlashMessage($_SESSION['success'], 'success');
                unset($_SESSION['success']);
            }
            
            if ($hasError) {
                $this->renderFlashMessage($_SESSION['error'], 'error');
                unset($_SESSION['error']);
            }
        ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const flashMessages = document.querySelectorAll('.flash-message');

        flashMessages.forEach(function(message) {
            const closeBtn = message.querySelector('.close-flash');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    message.classList.add('fade-out');
                    setTimeout(() => message.remove(), 500);
                });
            }
            setTimeout(function() {
                if (message.parentNode) {
                    message.classList.add('fade-out');
                    setTimeout(() => message.remove(), 500);
                }
            }, 2000);
        });
    });
    </script>
    <?php
        }
    }
    
    private function renderFlashMessage($message, $type)
    {
        if ($type === 'success') {
            $bgColor = 'bg-green-50';
            $borderColor = 'border-green-500';
            $textColor = 'text-green-800';
            $icon = '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>';
        } else {
            $bgColor = 'bg-red-50';
            $borderColor = 'border-red-500';
            $textColor = 'text-red-800';
            $icon = '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>';
        }
        ?>
    <div
        class="flash-message <?php echo $bgColor; ?> border-l-4 <?php echo $borderColor; ?> p-4 rounded-lg shadow-lg flex items-start gap-3">
        <div class="flex-shrink-0">
            <?php echo $icon; ?>
        </div>
        <div class="flex-grow">
            <p class="<?php echo $textColor; ?> font-medium">
                <?php echo $this->escape($message); ?>
            </p>
        </div>
        <button class="close-flash flex-shrink-0 text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>
    <?php
    }
}
?>