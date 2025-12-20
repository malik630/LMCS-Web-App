if (typeof ASSETS_URL === "undefined") {
  window.ASSETS_URL = "../assets/";
}

window.toggleProjetDetails = function (projetId) {
  const card = document.querySelector(
    `[data-projet*='"id_projet":${projetId}']`
  );
  const detailsDiv = document.getElementById(`details-${projetId}`);
  const button = card.querySelector('a[onclick*="toggleProjetDetails"]');

  if (!card || !detailsDiv) {
    console.error("Éléments non trouvés pour le projet", projetId);
    return;
  }

  // Toggle affichage
  if (detailsDiv.classList.contains("hidden")) {
    // Ouvrir
    if (!detailsDiv.hasAttribute("data-loaded")) {
      try {
        const data = JSON.parse(card.dataset.projet);
        detailsDiv.innerHTML = renderProjetDetails(data);
        detailsDiv.setAttribute("data-loaded", "true");
      } catch (e) {
        console.error("Erreur de parsing JSON:", e);
        detailsDiv.innerHTML =
          '<div class="bg-red-50 text-red-600 p-4 rounded">Erreur de chargement des détails</div>';
      }
    }
    detailsDiv.classList.remove("hidden");
    if (button)
      button.querySelector("span").textContent = "Masquer les détails";
  } else {
    // Fermer
    detailsDiv.classList.add("hidden");
    if (button)
      button.querySelector("span").textContent = "Voir plus de détails";
  }
};

function renderProjetDetails(data) {
  const projet = data.projet;
  const membres = data.membres || [];
  const publications = data.publications || [];
  const partenaires = data.partenaires || [];

  let html =
    '<div class="bg-gray-50 rounded-lg border border-gray-200 p-6 space-y-6">';

  // Informations détaillées
  html += '<div class="grid md:grid-cols-3 gap-6">';

  // Période
  if (projet.date_debut || projet.date_fin) {
    html += `
      <div class="bg-white p-4 rounded-lg border border-gray-200">
        <h4 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          Période
        </h4>
        <p class="text-gray-700 text-sm">
          <strong>Début:</strong> ${
            formatDate(projet.date_debut) || "Non spécifié"
          }<br>
          <strong>Fin:</strong> ${formatDate(projet.date_fin) || "En cours"}
        </p>
      </div>
    `;
  }

  // Budget
  if (projet.budget) {
    html += `
      <div class="bg-white p-4 rounded-lg border border-gray-200">
        <h4 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
          <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Budget
        </h4>
        <p class="text-gray-700 text-sm font-semibold">
          ${parseFloat(projet.budget).toLocaleString("fr-FR")} DA
        </p>
      </div>
    `;
  }

  // Fiche détaillée
  if (projet.fiche_detaillee) {
    html += `
      <div class="bg-white p-4 rounded-lg border border-gray-200">
        <h4 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
          <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Fiche détaillée
        </h4>
        <a href="${ASSETS_URL}documents/${escapeHtml(projet.fiche_detaillee)}" 
           target="_blank"
           class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-semibold text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Télécharger la fiche
        </a>
      </div>
    `;
  }

  html += "</div>";

  // Membres
  if (membres.length > 0) {
    html += `
      <div>
        <h4 class="font-bold text-gray-900 mb-3 text-lg flex items-center gap-2">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          Membres de l'équipe
          <span class="text-blue-600 text-base">(${membres.length})</span>
        </h4>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
    `;

    membres.forEach((membre) => {
      const initiales =
        (membre.prenom?.charAt(0) || "") + (membre.nom?.charAt(0) || "");
      const nomComplet = `${escapeHtml(membre.prenom || "")} ${escapeHtml(
        membre.nom || ""
      )}`.trim();
      const role = escapeHtml(membre.role_projet || "Membre");

      html += `
        <div class="flex items-center gap-3 p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition">
          <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0">
            ${initiales}
          </div>
          <div class="flex-grow min-w-0">
            <p class="font-semibold text-gray-900 truncate">${nomComplet}</p>
            <p class="text-xs text-gray-600 truncate">${role}</p>
          </div>
        </div>
      `;
    });

    html += "</div></div>";
  }

  // Publications
  if (publications.length > 0) {
    html += `
      <div>
        <h4 class="font-bold text-gray-900 mb-3 text-lg flex items-center gap-2">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
          Publications associées
          <span class="text-green-600 text-base">(${publications.length})</span>
        </h4>
        <div class="space-y-3">
    `;

    publications.forEach((pub) => {
      html += `
        <div class="p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition">
          <div class="flex items-start justify-between gap-3 mb-2 flex-wrap">
            <h5 class="font-bold text-gray-900 flex-grow">${escapeHtml(
              pub.titre
            )}</h5>
            <div class="flex items-center gap-2 flex-shrink-0">
              <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold text-xs whitespace-nowrap">
                ${escapeHtml(pub.type_libelle || "Publication")}
              </span>
              <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full font-semibold text-xs">
                ${pub.annee}
              </span>
            </div>
          </div>
          ${
            pub.auteurs
              ? `<p class="text-sm text-gray-600 mb-2"><strong>Auteurs:</strong> ${escapeHtml(
                  pub.auteurs
                )}</p>`
              : ""
          }
          ${
            pub.resume
              ? `<p class="text-sm text-gray-600 line-clamp-2">${escapeHtml(
                  pub.resume
                )}</p>`
              : ""
          }
        </div>
      `;
    });

    html += "</div></div>";
  }

  // Partenaires
  if (partenaires.length > 0) {
    html += `
      <div>
        <h4 class="font-bold text-gray-900 mb-3 text-lg flex items-center gap-2">
          <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
          Partenaires
          <span class="text-purple-600 text-base">(${partenaires.length})</span>
        </h4>
        <div class="flex flex-wrap gap-3">
    `;

    partenaires.forEach((part) => {
      html += `
        <div class="px-4 py-2 bg-white rounded-lg border-2 border-purple-200 hover:border-purple-400 transition">
          <p class="font-semibold text-gray-900">${escapeHtml(part.nom)}</p>
          ${
            part.type
              ? `<p class="text-xs text-gray-600">${escapeHtml(part.type)}</p>`
              : ""
          }
        </div>
      `;
    });

    html += "</div></div>";
  }

  html += "</div>";
  return html;
}

function escapeHtml(text) {
  if (!text) return "";
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

function formatDate(date) {
  if (!date) return "";
  const d = new Date(date);
  if (isNaN(d.getTime())) return date;
  return d.toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}
