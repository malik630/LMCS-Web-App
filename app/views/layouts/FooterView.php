<?php

class FooterView extends View
{
    public function render()
    {
        $this->renderFooterContainer();
        $this->renderScripts();
        $this->renderClosingTags();
    }
    
    private function renderFooterContainer(){
        ?>
<footer class="bg-black text-white mt-12 py-8">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8">
            <?php $this->renderContactSection(); ?>
            <?php $this->renderQuickLinksSection(); ?>
            <?php $this->renderAboutSection(); ?>
        </div>
        <?php $this->renderCopyright(); ?>
    </div>
</footer>
<?php
    }
    
    private function renderContactSection()
    {
        ?>
<div>
    <h3 class="text-xl font-bold mb-4">Contact</h3>
    <p class="mb-2"><strong>Adresse:</strong> LMCS, Ecole nationale Supérieure d’Informatique, BP M68, Oued Smar, Alger
        16309</p>
    <p class="mb-2"><strong>Téléphone/Fax:</strong> 00 213 (0) 23-93-91-30</p>
    <p class="mb-2"><strong>Email:</strong> lmcs@esi.dz</p>
</div>
<?php
    }
    
    private function renderQuickLinksSection()
    {
        $links = [
            '' => 'Accueil',
            'team' => 'Équipes',
            'publication' => 'Publications',
            'contact' => 'Contact'
        ];
        
        ?>
<div>
    <h3 class="text-xl font-bold mb-4">Liens Rapides</h3>
    <ul class="space-y-2">
        <?php foreach ($links as $url => $label): ?>
        <li><a href="<?php echo BASE_URL . $url; ?>" class="hover:text-blue-400 transition"><?php echo $label; ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php
    }
    
    private function renderAboutSection()
    {
        ?>
<div>
    <h3 class="text-xl font-bold mb-4">À propos</h3>
    <img src="<?php echo ASSETS_URL; ?>images/ESI-Logo-White.png" alt="Logo ESI" class="h-16 mb-4"
        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22%3E%3Crect width=%2260%22 height=%2260%22 fill=%22%23555%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22white%22 font-size=%2210%22%3EUNIVERSITE%3C/text%3E%3C/svg%3E'">
    <p class="text-sm">École Supérieure d'Informatique</p>
</div>
<?php
    }
    
    private function renderCopyright()
    {
        ?>
<div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm">
    <p>&copy; <?php echo date('Y'); ?> Laboratoire LMCS ESI. Tous droits réservés.</p>
</div>
<?php
    }
    
    private function renderScripts()
    {
        ?>
<script src="<?php echo ASSETS_URL; ?>js/slider.js"></script>
<script src="<?php echo ASSETS_URL; ?>js/pagination.js"></script>
<?php
    }
    
    private function renderClosingTags()
    {
        echo '</body></html>';
    }
}
?>