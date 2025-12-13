<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';

class ProfileView extends View
{
    protected $pageTitle = 'Mon Profil - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $this->renderProfile();
        $this->renderFooter();
    }
    
    private function renderProfile()
    {
        $user = $this->get('user');
        $documents = $this->get('documents', []);
        
        ?>
<div class="container mx-auto px-4 py-8">
    <!-- Titre -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white mb-2">Mon Profil</h1>
        <a href="<?php echo BASE_URL; ?>dashboard" class="text-white hover:text-blue-200 transition">
            ← Retour au tableau de bord
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Colonne gauche : Photo et infos de base -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                <!-- Photo de profil -->
                <div class="text-center mb-6">
                    <?php $this->renderUserPhoto($user); ?>
                    <h2 class="text-xl font-bold text-gray-900 mt-4">
                        <?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?>
                    </h2>
                    <p class="text-gray-600"><?php echo $this->escape($user['grade']); ?></p>
                    <?php if (!empty($user['poste'])): ?>
                    <p class="text-gray-500 text-sm"><?php echo $this->escape($user['poste']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Informations de compte -->
                <div class="border-t pt-4 space-y-2 text-sm">
                    <p><span class="font-semibold">Username:</span> <?php echo $this->escape($user['username']); ?></p>
                    <p><span class="font-semibold">Email:</span> <?php echo $this->escape($user['email']); ?></p>
                    <p><span class="font-semibold">Rôle:</span> <?php echo $this->escape(ucfirst($user['role'])); ?></p>
                    <p><span class="font-semibold">Membre depuis:</span>
                        <?php echo DateHelper::format($user['date_creation'], 'd/m/Y'); ?></p>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Formulaires -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Modifier les informations personnelles -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6 text-gray-900">Informations personnelles</h2>
                <form action="<?php echo BASE_URL; ?>dashboard/updateProfile" method="POST"
                    enctype="multipart/form-data">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nom</label>
                            <input type="text" name="nom" value="<?php echo $this->escape($user['nom']); ?>" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Prénom</label>
                            <input type="text" name="prenom" value="<?php echo $this->escape($user['prenom']); ?>"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" name="email" value="<?php echo $this->escape($user['email']); ?>"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Grade</label>
                            <input type="text" name="grade" value="<?php echo $this->escape($user['grade']); ?>"
                                class="w-full px-4 py-2 text-gray-500 cursor-not-allowed border border-gray-300 rounded-lg"
                                disabled>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Poste</label>
                            <input type="text" name="poste" value="<?php echo $this->escape($user['poste']); ?>"
                                class="w-full px-4 py-2 text-gray-500 cursor-not-allowed border border-gray-300 rounded-lg"
                                disabled>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Domaine de recherche</label>
                            <input type="text" name="domaine_recherche"
                                value="<?php echo $this->escape($user['domaine_recherche']); ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Biographie</label>
                            <textarea name="biographie" rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $this->escape($user['biographie']); ?></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Photo de profil</label>
                            <input type="file" name="photo" accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Formats acceptés: JPG, PNG, GIF (max 2 MB)</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- Changer le mot de passe -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6 text-gray-900">Changer le mot de passe</h2>
                <form action="<?php echo BASE_URL; ?>dashboard/changePassword" method="POST">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Mot de passe actuel *</label>
                            <input type="password" name="old_password" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nouveau mot de passe *</label>
                            <input type="password" name="new_password" required minlength="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Minimum 6 caractères</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Confirmer le nouveau mot de passe
                                *</label>
                            <input type="password" name="confirm_password" required minlength="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                            class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                            Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>

            <!-- Documents personnels -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6 text-gray-900">Documents personnels</h2>

                <!-- Formulaire d'ajout -->
                <form action="<?php echo BASE_URL; ?>dashboard/uploadDocument" method="POST"
                    enctype="multipart/form-data" class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-bold text-gray-900 mb-4">Ajouter un document</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Titre *</label>
                            <input type="text" name="titre" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Type</label>
                            <select name="type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Non spécifié</option>
                                <option value="CV">CV</option>
                                <option value="Diplôme">Diplôme</option>
                                <option value="Certificat">Certificat</option>
                                <option value="Publication">Publication</option>
                                <option value="Rapport">Rapport</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Fichier *</label>
                            <input type="file" name="document" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Formats acceptés: PDF, DOC, DOCX, XLS, XLSX, PPT,
                                PPTX, ZIP (max 10 MB)</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit"
                            class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                            Ajouter le document
                        </button>
                    </div>
                </form>

                <!-- Liste des documents -->
                <?php if (!empty($documents)): ?>
                <div class="space-y-3">
                    <h3 class="font-bold text-gray-900 mb-3">Mes documents (<?php echo count($documents); ?>)</h3>
                    <?php foreach ($documents as $doc): ?>
                    <div
                        class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                        <div class="flex-grow">
                            <h4 class="font-bold text-gray-900"><?php echo $this->escape($doc['titre']); ?></h4>
                            <div class="flex items-center gap-4 text-sm text-gray-600 mt-1">
                                <?php if (!empty($doc['type'])): ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">
                                    <?php echo $this->escape($doc['type']); ?>
                                </span>
                                <?php endif; ?>
                                <span><?php echo DateHelper::format($doc['date_upload'], 'd/m/Y'); ?></span>
                                <span><?php echo round($doc['taille_fichier'] / 1024, 2); ?> KB</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="<?php echo ASSETS_URL . 'documents/' . $doc['fichier']; ?>" target="_blank"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                                Télécharger
                            </a>
                            <a href="<?php echo BASE_URL . 'dashboard/deleteDocument/' . $doc['id_document']; ?>"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm">
                                Supprimer
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-center py-6">Aucun document</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
    }
    
    private function renderUserPhoto($user)
    {
        if (!empty($user['photo'])) {
            $src = ASSETS_URL . 'images/users/' . $user['photo'];
            echo '<img src="' . $src . '" alt="Photo de profil" class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-blue-600" onerror="this.src=\'' . ImageHelper::placeholder(128, 128, '#667eea') . '\'">';
        } else {
            echo '<div class="w-32 h-32 rounded-full bg-blue-600 flex items-center justify-center text-white text-4xl font-bold mx-auto border-4 border-blue-700">';
            echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1));
            echo '</div>';
        }
    }
}
?>