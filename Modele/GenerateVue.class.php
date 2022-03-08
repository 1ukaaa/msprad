<?php
/**
 * Class Vue
 * Génère la vue
 */
class GenerateVue {
    private $nomFichier;

    public function __construct($action, $donnees) {
        $nomVue = "Vue".$action;
        $this->nomFichier = "Vue/". $nomVue . ".php";
        $this->donnees = $donnees;
    }
    
    // Génère et affiche la vue
    public function generer() {
        $contenu = $this->getVue($this->nomFichier, $this->donnees);
        $vue = $this->getVue('Vue/gabarit.php', array('contenu' => $contenu));
        echo $vue;
    }

    private function getVue($nomFichier, $donnees) {
        return $this->genererFichier($nomFichier, $donnees);
    }

    // Génère un fichier vue et renvoie le résultat produit
    private function genererFichier($fichier, $donnees) {
        if (file_exists($fichier)) {
            extract($donnees);
            ob_start();
            require $fichier;
            return ob_get_clean();
        }
        else {
            throw new Exception("Fichier '$fichier' introuvable");
        }
    }
}