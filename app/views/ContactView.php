<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Section.php';
require_once __DIR__ . '/components/Form.php';

class ContactView extends View
{
    protected $pageTitle = 'Contact - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        
        PageHeader::render([
            'title' => 'Contactez-nous',
            'subtitle' => 'Nous sommes à votre écoute'
        ]);
        
        echo '<div class="grid md:grid-cols-2 gap-8 mb-8">';
        $this->renderContactInfo();
        $this->renderContactForm();
        echo '</div>';
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderContactInfo()
    {
        Section::create('Informations de contact', function() {
            ?>
<div class="space-y-6">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <?= HtmlHelper::icon('location', 'w-6 h-6 text-blue-600') ?>
        </div>
        <div>
            <h3 class="font-semibold text-gray-900 mb-1">Adresse</h3>
            <p class="text-gray-600">
                École nationale Supérieure d'Informatique (ESI)<br>
                BP 68M, Oued Smar, 16309<br>
                Alger, Algérie
            </p>
        </div>
    </div>

    <div class="flex items-start gap-4">
        <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <?= HtmlHelper::icon('email', 'w-6 h-6 text-green-600') ?>
        </div>
        <div>
            <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
            <a href="mailto:lmcs@esi.dz" class="text-blue-600 hover:text-blue-800">
                lmcs@esi.dz
            </a>
        </div>
    </div>

    <div class="flex items-start gap-4">
        <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <?= HtmlHelper::icon('phone', 'w-6 h-6 text-purple-600') ?>
        </div>
        <div>
            <h3 class="font-semibold text-gray-900 mb-1">Téléphone</h3>
            <p class="text-gray-600">+213 (0) 23-93-91-30</p>
        </div>
    </div>

    <div class="flex items-start gap-4">
        <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
            <?= HtmlHelper::icon('clock', 'w-6 h-6 text-orange-600') ?>
        </div>
        <div>
            <h3 class="font-semibold text-gray-900 mb-1">Horaires</h3>
            <p class="text-gray-600">
                Dimanche - Jeudi: 8h00 - 17h00<br>
                Vendredi - Samedi: Fermé
            </p>
        </div>
    </div>
</div>
<?php
        }, 'bg-white');
    }
    
    private function renderContactForm()
    {
        Section::create('Envoyez-nous un message', function() {
            Form::render([
                'action' => BASE_URL . 'contact/send',
                'method' => 'POST',
                'class' => 'space-y-6',
                'fields' => [
                    [
                        'type' => 'text',
                        'name' => 'nom',
                        'label' => 'Nom complet',
                        'placeholder' => 'Votre nom et prénom',
                        'required' => true,
                        'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent'
                    ],
                    [
                        'type' => 'email',
                        'name' => 'email',
                        'label' => 'Email',
                        'placeholder' => 'votre.email@example.com',
                        'required' => true,
                        'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent'
                    ],
                    [
                        'type' => 'text',
                        'name' => 'sujet',
                        'label' => 'Sujet',
                        'placeholder' => 'Objet de votre message',
                        'required' => true,
                        'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent'
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'message',
                        'label' => 'Message',
                        'placeholder' => 'Votre message...',
                        'rows' => 6,
                        'required' => true,
                        'helper' => 'Minimum 10 caractères',
                        'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent'
                    ]
                ],
                'buttons' => [
                    [
                        'type' => 'submit',
                        'text' => 'Envoyer le message',
                        'style' => 'primary',
                        'icon' => 'message'
                    ]
                ]
            ]);
        }, 'bg-white');
    }
}
?>