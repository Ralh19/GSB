<?php

/**
 * Classe d'accès aux données.
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */
/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $connexion de type PDO
 * $instance qui contiendra l'unique instance de la classe
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

namespace Modeles;

use PDO;
use Outils\Utilitaires;

require '../config/bdd.php';

class PdoGsb {

    protected $connexion;
    private static $instance = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct() {
        $this->connexion = new PDO(DB_DSN, DB_USER, DB_PWD);
        $this->connexion->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct() {
        $this->connexion = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb(): PdoGsb {
        if (self::$instance == null) {
            self::$instance = new PdoGsb();
        }
        return self::$instance;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosUtilisateur($login, $mdp) {
        // Vérifier dans la table visiteur
        $requetePrepare = $this->connexion->prepare(
                'SELECT id, nom, prenom, "visiteur" AS type FROM visiteur WHERE login = :unLogin AND mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        $visiteur = $requetePrepare->fetch();

        if ($visiteur) {
            return $visiteur;
        }

        $requetePrepare = $this->connexion->prepare(
                'SELECT id, nom, prenom, "comptable" AS type FROM comptable WHERE login = :unLogin AND mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        $comptable = $requetePrepare->fetch();

        return $comptable;
    }

    public function getLesVisiteurs(): array {
        $req = "SELECT id, nom, prenom FROM visiteur"; // Remplace par la bonne requête SQL pour récupérer les visiteurs
        $stmt = $this->connexion->prepare($req);
        $stmt->execute();

        $lesVisiteurs = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lesVisiteurs[] = $row;
        }

        return $lesVisiteurs;
    }

    public function getMoisDisponibles(): array {
        $req = "SELECT DISTINCT mois FROM fichefrais ORDER BY mois DESC";
        $stmt = $this->connexion->prepare($req);
        $stmt->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $stmt->execute();

        // Initialisation du tableau pour les mois
        $lesMois = [];

        // Récupérer les résultats
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mois = $row['mois'];
            $numAnnee = substr($mois, 0, 4); // Extraire l'année
            $numMois = substr($mois, 4, 2);   // Extraire le mois
            $lesMois[] = [
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            ];
        }

        return $lesMois;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0;
                $i < $nbLignes;
                $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = Utilitaires::dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    public function validerFicheFrais($idVisiteur, $mois) {
        $req = "UPDATE fichefrais SET idetat = 'VA' WHERE idvisiteur = :idVisiteur AND mois = :mois";
        $stmt = PdoGsb::$monPdo->prepare($req);
        $stmt->bindParam(':idVisiteur', $idVisiteur);
        $stmt->bindParam(':mois', $mois);
        $stmt->execute();
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois): int {
        $requetePrepare = $this->connexion->prepare(
                'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT fraisforfait.id as idfrais, '
                . 'fraisforfait.libelle as libelle, '
                . 'lignefraisforfait.quantite as quantite '
                . 'FROM lignefraisforfait '
                . 'INNER JOIN fraisforfait '
                . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais(): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT fraisforfait.id as idfrais '
                . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais): void {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = $this->connexion->prepare(
                    'UPDATE lignefraisforfait '
                    . 'SET lignefraisforfait.quantite = :uneQte '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs): void {
        $requetePrepare = $this->connexion->prepare(
                'UPDATE fichefrais '
                . 'SET nbjustificatifs = :unNbJustificatifs '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
                ':unNbJustificatifs',
                $nbJustificatifs,
                PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois): bool {
        $boolReturn = false;
        $requetePrepare = $this->connexion->prepare(
                'SELECT fichefrais.mois FROM fichefrais '
                . 'WHERE fichefrais.mois = :unMois '
                . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur): string {
        $requetePrepare = $this->connexion->prepare(
                'SELECT MAX(mois) as dernierMois '
                . 'FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois): void {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = $this->connexion->prepare(
                'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
                . 'montantvalide,datemodif,idetat) '
                . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = $this->connexion->prepare(
                    'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                    . 'idfraisforfait,quantite) '
                    . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais['idfrais'], PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant): void {
        $dateFr = Utilitaires::dateFrancaisVersAnglais($date);
        $requetePrepare = $this->connexion->prepare(
                'INSERT INTO lignefraishorsforfait '
                . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
                . ':unMontant) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais): void {
        $requetePrepare = $this->connexion->prepare(
                'DELETE FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur): array {
        // Requête SQL pour récupérer les mois où l'état est "CR" ou "CL"
        $requetePrepare = $this->connexion->prepare(
                'SELECT fichefrais.mois AS mois FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.idetat IN ("CR", "CL") '
                . 'ORDER BY fichefrais.mois DESC'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();

        $lesMois = [];
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4); // Extraire l'année
            $numMois = substr($mois, 4, 2);  // Extraire le mois

            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }

        return $lesMois;
    }

    public function getVisiteurInfo($idVisiteur) {
        $req = "SELECT nom, prenom FROM visiteur WHERE id = :idVisiteur";
        $stmt = $this->connexion->prepare($req);
        $stmt->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $stmt->execute();

        // Récupérer les informations du visiteur
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT fichefrais.idetat as idEtat, '
                . 'fichefrais.datemodif as dateModif,'
                . 'fichefrais.nbjustificatifs as nbJustificatifs, '
                . 'fichefrais.montantvalide as montantValide, '
                . 'etat.libelle as libEtat '
                . 'FROM fichefrais '
                . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur,
                PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    public function getLesInfosFicheFrais2($idVisiteur, $mois): array {
        // Récupérer les informations de la fiche de frais
        $requetePrepare = $this->connexion->prepare(
                'SELECT fichefrais.idetat as idEtat, '
                . 'fichefrais.datemodif as dateModif, '
                . 'fichefrais.nbjustificatifs as nbJustificatifs, '
                . 'fichefrais.montantvalide as montantValide, '
                . 'etat.libelle as libEtat '
                . 'FROM fichefrais '
                . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $ficheFrais = $requetePrepare->fetch(PDO::FETCH_ASSOC);

        // Récupérer les éléments forfaitisés pour ce visiteur et ce mois
        $requetePrepareForfait = $this->connexion->prepare(
                'SELECT lignefraisforfait.idfraisforfait, lignefraisforfait.quantite, fraisforfait.libelle '
                . 'FROM lignefraisforfait '
                . 'INNER JOIN fraisforfait ON lignefraisforfait.idfraisforfait = fraisforfait.id '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois'
        );
        $requetePrepareForfait->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepareForfait->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepareForfait->execute();
        $elementsForfaitises = [];
        while ($row = $requetePrepareForfait->fetch(PDO::FETCH_ASSOC)) {
            $elementsForfaitises[] = [
                'idfraisforfait' => $row['idfraisforfait'],
                'quantite' => $row['quantite'],
                'libelle' => $row['libelle']
            ];
        }

        // Retourner à la fois les informations de la fiche de frais et les éléments forfaitisés
        return [
            'ficheFrais' => $ficheFrais,
            'elementsForfaitises' => $elementsForfaitises
        ];
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat): void {
        $requetePrepare = $this->connexion->prepare(
                'UPDATE fichefrais '
                . 'SET idetat = :unEtat, datemodif = now() '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    public function getElementsHorsForfait($idVisiteur, $mois): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT libelle, date, montant 
         FROM lignefraishorsforfait
         WHERE idvisiteur = :unIdVisiteur 
         AND mois = :unMois'
        );

        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();

        $elementsHorsForfait = [];
        while ($row = $requetePrepare->fetch(PDO::FETCH_ASSOC)) {
            $elementsHorsForfait[] = [
                'date' => $row['date'],
                'libelle' => $row['libelle'],
                'montant' => $row['montant']
            ];
        }


        return $elementsHorsForfait;
    }

    public function updateTempFraisForfait($idVisiteur, $mois, $idFraisForfait, $quantite, $typeVehicule = null) {
        $requete = $this->connexion->prepare(
                'UPDATE temp_lignefraisforfait
         SET quantite = :quantite, typeVehicule = :typeVehicule
         WHERE idvisiteur = :idVisiteur AND mois = :mois AND idfraisforfait = :idFraisForfait'
        );

        $requete->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete->bindParam(':mois', $mois, PDO::PARAM_STR);
        $requete->bindParam(':idFraisForfait', $idFraisForfait, PDO::PARAM_STR);
        $requete->bindParam(':quantite', $quantite, PDO::PARAM_INT);
        $requete->bindParam(':typeVehicule', $typeVehicule, PDO::PARAM_STR);

        $requete->execute();
    }

    public function reinitialiserTempFraisForfait($idVisiteur, $mois) {
        $requete = $this->connexion->prepare(
                'UPDATE temp_lignefraisforfait tf
         INNER JOIN lignefraisforfait lf
         ON tf.idvisiteur = lf.idvisiteur AND tf.mois = lf.mois AND tf.idfraisforfait = lf.idfraisforfait
         SET tf.quantite = lf.quantite, tf.typeVehicule = lf.typeVehicule
         WHERE tf.idvisiteur = :idVisiteur AND tf.mois = :mois'
        );

        $requete->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete->bindParam(':mois', $mois, PDO::PARAM_STR);

        $requete->execute();
    }

    public function validerFraisForfait($idVisiteur, $mois) {
        $this->connexion->prepare(
                'UPDATE lignefraisforfait lff
         JOIN temp_lignefraisforfait tlf
         ON lff.idvisiteur = tlf.idvisiteur AND lff.mois = tlf.mois AND lff.idfraisforfait = tlf.idfraisforfait
         SET lff.quantite = tlf.quantite, lff.typeVehicule = tlf.typeVehicule
         WHERE lff.idvisiteur = :idVisiteur AND lff.mois = :mois'
        )->execute([':idVisiteur' => $idVisiteur, ':mois' => $mois]);

        // Supprimer les données de la table temporaire après validation
        $this->connexion->prepare(
                'DELETE FROM temp_lignefraisforfait WHERE idvisiteur = :idVisiteur AND mois = :mois'
        )->execute([':idVisiteur' => $idVisiteur, ':mois' => $mois]);
    }

    public function getCopieFraisForfait($idVisiteur, $mois): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT tlf.idfraisforfait, tlf.quantite, tlf.typeVehicule, ff.libelle
         FROM temp_lignefraisforfait tlf
         INNER JOIN fraisforfait ff ON tlf.idfraisforfait = ff.id
         WHERE tlf.idvisiteur = :idVisiteur AND tlf.mois = :mois'
        );

        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':mois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();

        $elementsForfaitises = [];
        while ($row = $requetePrepare->fetch(PDO::FETCH_ASSOC)) {
            $elementsForfaitises[] = [
                'idfraisforfait' => $row['idfraisforfait'],
                'quantite' => $row['quantite'],
                'libelle' => $row['libelle'],
                'typeVehicule' => $row['typeVehicule'] // Nouveau champ
            ];
        }

        return $elementsForfaitises;
    }

    public function validerCopieFraisForfait(string $idVisiteur, string $mois): void {
        // Supprimer les anciens enregistrements dans la table principale
        $requeteDelete = $this->connexion->prepare(
                'DELETE FROM lignefraisforfait WHERE idvisiteur = :idVisiteur AND mois = :mois'
        );
        $requeteDelete->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requeteDelete->bindParam(':mois', $mois, PDO::PARAM_STR);
        $requeteDelete->execute();

        // Insérer les enregistrements depuis la table temporaire
        $requeteInsert = $this->connexion->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur, mois, idfraisforfait, quantite, typeVehicule)
         SELECT idvisiteur, mois, idfraisforfait, quantite, typeVehicule
         FROM temp_lignefraisforfait
         WHERE idvisiteur = :idVisiteur AND mois = :mois'
        );
        $requeteInsert->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requeteInsert->bindParam(':mois', $mois, PDO::PARAM_STR);
        $requeteInsert->execute();
    }

    public function calculerIndemniteKilometrique($idVisiteur, $distance) {
        // Récupération du type de véhicule déclaré
        $requetePrepare = $this->connexion->prepare(
                'SELECT typeVehicule FROM visiteur WHERE id = :idVisiteur'
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();

        $typeVehicule = $requetePrepare->fetchColumn();

        // Tarifs en fonction du type de véhicule
        $tarifs = [
            '4CV Diesel' => 0.52,
            '5/6CV Diesel' => 0.58,
            '4CV Essence' => 0.62,
            '5/6CV Essence' => 0.67
        ];

        // Calcul de l'indemnité kilométrique
        if (isset($tarifs[$typeVehicule])) {
            return $distance * $tarifs[$typeVehicule];
        } else {
            return 0; // Si le type de véhicule est inconnu ou non déclaré
        }
    }

    public function getTypeVehiculeVisiteur($idVisiteur) {
        $requete = "SELECT typeVehicule FROM visiteur WHERE id = :idVisiteur";
        $stmt = $this->connexion->prepare($requete);
        $stmt->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['typeVehicule'] ?? '4CV Diesel'; // Valeur par défaut
    }

    public function getFraisForfait($idVisiteur, $mois): array {
        $requetePrepare = $this->connexion->prepare(
                'SELECT lff.idfraisforfait, lff.quantite, ff.libelle, lff.typeVehicule
         FROM lignefraisforfait lff
         INNER JOIN fraisforfait ff ON lff.idfraisforfait = ff.id
         WHERE lff.idvisiteur = :idVisiteur AND lff.mois = :mois'
        );

        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':mois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();

        $fraisForfait = [];
        while ($row = $requetePrepare->fetch(PDO::FETCH_ASSOC)) {
            $fraisForfait[] = [
                'idfraisforfait' => $row['idfraisforfait'],
                'quantite' => $row['quantite'],
                'libelle' => $row['libelle'],
                'typeVehicule' => $row['typeVehicule'] ?? null // Inclure typeVehicule si disponible
            ];
        }

        return $fraisForfait;
    }
}
