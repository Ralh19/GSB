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

class PdoGsb
{

    protected $connexion;
    private static $instance = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        $this->connexion = new PDO(DB_DSN, DB_USER, DB_PWD);
        $this->connexion->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        $this->connexion = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb(): PdoGsb
    {
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
    public function getInfosVisiteur($login) {
        $requetePrepare = $this->connexion->prepare(
            'SELECT id, nom, prenom, email FROM visiteur WHERE login = :unLogin'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }
    

    /**
 * Retourne le nom et le prénom d'un visiteur à partir de son identifiant
 *
 * @param string $idVisiteur Identifiant du visiteur
 *
 * @return array Tableau associatif contenant 'nom' et 'prenom'
 */
public function getNomPrenomVisiteurParId(string $idVisiteur): array
{
    $requetePrepare = $this->connexion->prepare(
        'SELECT nom, prenom 
         FROM visiteur 
         WHERE id = :unIdVisiteur'
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->execute();

    $resultat = $requetePrepare->fetch(PDO::FETCH_ASSOC);
    return $resultat ? $resultat : ['nom' => '', 'prenom' => ''];
}

    
    /**
     * Hash tous les mots de passe des visiteurs avec password_hash()
     */
    public function setHashMdp() {
        $requete = $this->connexion->prepare('SELECT id, mdp FROM visiteur');
        $requete-> execute();
        $lignes = $requete->fetchAll(PDO::FETCH_ASSOC);
        foreach($lignes as $array) {
            $id = $array["id"];
            $mdp = $array["mdp"];
            $hashMdp = password_hash($mdp, PASSWORD_DEFAULT);
            $req = $this->connexion->prepare('UPDATE visiteur SET mdp = :hashMdp WHERE id= :unId ');
            $req->bindParam('unId',$id, PDO::PARAM_STR);
            $req->bindParam(':hashMdp', $hashMdp, PDO::PARAM_STR);
            $req->execute();
        }
    }
    
    public function setHashMdpComptables() {
        $requete = $this->connexion->prepare('SELECT idcomptable, mdp FROM comptable');
        $requete->execute();
        $lignes = $requete->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($lignes as $array) {
            $id = $array["idcomptable"];
            $mdp = $array["mdp"];
            $hashMdp = password_hash($mdp, PASSWORD_DEFAULT);
            $req = $this->connexion->prepare(
                'UPDATE comptable SET mdp = :hashMdp WHERE idcomptable = :unId'
            );
            $req->bindParam(':unId', $id, PDO::PARAM_STR);
            $req->bindParam(':hashMdp', $hashMdp, PDO::PARAM_STR);
            $req->execute();
        }
    }
    

    /**
     * Retourne le mot de passe brut d'un visiteur (attention : à utiliser uniquement pour migration vers hash)
     * @param string $login
     * @return string mot de passe
     */
    public function getMdpVisiteur($login) {
        $requetePrepare = $this->connexion->prepare(
            'SELECT mdp FROM visiteur WHERE login = :unLogin'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->execute();
        $result = $requetePrepare->fetch(PDO::FETCH_OBJ);
        return $result ? $result->mdp : null;
    }
    
    

    /**
     * Retourne le mot de passe brut d'un comptable (attention : à utiliser uniquement pour migration vers hash)
     * @param string $login
     * @return string mot de passe
     */
    public function getMdpComptable($login) {
        $sql = 'SELECT mdp FROM comptable WHERE login = :login';
        $req = $this->connexion->prepare($sql);
        $req->bindParam(':login', $login, PDO::PARAM_STR);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_OBJ);
        return $result ? $result->mdp : null;
    }
    
    
    

    /**
     * Retourne tous les visiteurs
     * @return array liste des visiteurs
     */
    public function getLesVisiteurs(): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT * FROM visiteur'
        );
        $requetePrepare->execute();
        $resultat = $requetePrepare->fetchAll();
        return $resultat ? $resultat : [];
    }

    /**
     * Récupère les infos d'un comptable
     * @param string $login
     * @param string $mdp
     * @return array
     */
    public function getInfosComptable($login) {
        $sql = 'SELECT idcomptable AS id, nom, prenom, email FROM comptable WHERE login = :login';
        $req = $this->connexion->prepare($sql);
        $req->bindParam(':login', $login, PDO::PARAM_STR);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    }
    
    public function setCodeA2f($id, $code) {
        $requetePrepare = $this->connexion->prepare(
            'UPDATE visiteur '
          . 'SET codea2f = :unCode '
          . 'WHERE visiteur.id = :unIdVisiteur '
        );
        $requetePrepare->bindParam(':unCode', $code, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    public function getCodeVisiteur($id) {
        $requetePrepare = $this->connexion->prepare(
            'SELECT visiteur.codea2f AS codea2f '
          . 'FROM visiteur '
          . 'WHERE visiteur.id = :unId'
        );
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch()['codea2f'];
    }

    public function setCodeA2fComptable($id, $code) {
        $requetePrepare = $this->connexion->prepare(
            'UPDATE comptable SET codea2f = :unCode WHERE idcomptable = :unIdComptable'
        );
        $requetePrepare->bindParam(':unCode', $code, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdComptable', $id, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    public function getCodeComptable($id) {
        $requetePrepare = $this->connexion->prepare(
            'SELECT codea2f FROM comptable WHERE idcomptable = :unId'
        );
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch(PDO::FETCH_COLUMN);
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
    public function getLesFraisHorsForfait($idVisiteur, $mois): array
    {
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
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = Utilitaires::dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois): int
    {
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
    public function getLesFraisForfait($idVisiteur, $mois): array
{
    $requetePrepare = $this->connexion->prepare(
        'SELECT fraisforfait.id AS idfrais, 
                fraisforfait.libelle AS libelle, 
                lignefraisforfait.quantite AS quantite 
         FROM lignefraisforfait 
         INNER JOIN fraisforfait 
           ON fraisforfait.id = lignefraisforfait.idfraisforfait 
         WHERE lignefraisforfait.idvisiteur = :unIdVisiteur 
           AND lignefraisforfait.mois = :unMois 
         ORDER BY 
           CASE fraisforfait.id
               WHEN "ETP" THEN 1
               WHEN "NUI" THEN 2
               WHEN "REP" THEN 3
               WHEN "V4" THEN 4
               WHEN "V5" THEN 4
               WHEN "E4" THEN 4
               WHEN "E5" THEN 4
               WHEN "KM" THEN 4
               ELSE 99
           END'
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
    return $requetePrepare->fetchAll();
}

/**
 * Retourne les ID des frais forfaitaires correspondant à un véhicule
 *
 * @return array Liste des IDs de frais kilométriques (ex: ['KM', 'V4', 'V5', 'E4', 'E5'])
 */
public function getIdsFraisKilometriques(): array
{
    $requetePrepare = $this->connexion->prepare(
        'SELECT id FROM fraisforfait 
         WHERE libelle LIKE "%Kilométrique%" 
            OR libelle LIKE "Véhicule%"'
    );
    $requetePrepare->execute();
    return array_column($requetePrepare->fetchAll(PDO::FETCH_ASSOC), 'id');
}


/**
 * Retourne les montants unitaires de chaque frais forfaitaire
 *
 * @return array Tableau associatif avec les IDs (ETP, KM, NUI, REP) comme clés
 *               et les montants correspondants comme valeurs.
 */
public function getMontantsFraisForfait(): array
{
    $requetePrepare = $this->connexion->prepare(
        'SELECT id, montant FROM fraisforfait'
    );
    $requetePrepare->execute();
    $resultat = $requetePrepare->fetchAll(PDO::FETCH_ASSOC);

    $montants = [];
    foreach ($resultat as $row) {
        $montants[$row['id']] = (float) $row['montant'];
    }

    return $montants;
}


    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais(): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT fraisforfait.id as idfrais '
                . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    public function majFraisForfait($idVisiteur, $mois, $lesFrais): void
    {
        foreach ($lesFrais as $idFrais => $quantite) {
            // Vérifie si la ligne existe
            $requeteVerif = $this->connexion->prepare(
                'SELECT COUNT(*) FROM lignefraisforfait 
                 WHERE idvisiteur = :idVisiteur AND mois = :mois AND idfraisforfait = :idFrais'
            );
            $requeteVerif->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requeteVerif->bindParam(':mois', $mois, PDO::PARAM_STR);
            $requeteVerif->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
            $requeteVerif->execute();
            $existe = $requeteVerif->fetchColumn() > 0;
    
            if ($existe) {
                // Mise à jour si la ligne existe
                $requeteMaj = $this->connexion->prepare(
                    'UPDATE lignefraisforfait 
                     SET quantite = :quantite 
                     WHERE idvisiteur = :idVisiteur AND mois = :mois AND idfraisforfait = :idFrais'
                );
            } else {
                // Insertion si la ligne n'existe pas
                $requeteMaj = $this->connexion->prepare(
                    'INSERT INTO lignefraisforfait (idvisiteur, mois, idfraisforfait, quantite)
                     VALUES (:idVisiteur, :mois, :idFrais, :quantite)'
                );
            }
    
            $requeteMaj->bindParam(':quantite', $quantite, PDO::PARAM_INT);
            $requeteMaj->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requeteMaj->bindParam(':mois', $mois, PDO::PARAM_STR);
            $requeteMaj->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
            $requeteMaj->execute();
        }
    }
    
    /**
 * Supprime tous les types de frais kilométriques pour un visiteur donné
 */
public function supprimerFraisKilometrique(string $idVisiteur, string $mois): void
{
    $types = ['KM', 'V4', 'V5', 'E4', 'E5'];

    $requete = $this->connexion->prepare(
        'DELETE FROM lignefraisforfait 
         WHERE idvisiteur = :idVisiteur AND mois = :mois 
         AND idfraisforfait IN ("KM", "V4", "V5", "E4", "E5")'
    );
    $requete->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requete->bindParam(':mois', $mois, PDO::PARAM_STR);
    $requete->execute();
}


    /**
 * Met à jour un frais hors forfait dans la base de données.
 *
 * Cette méthode permet de modifier un frais hors forfait existant pour un visiteur
 * et un mois donné. Elle met à jour la date, le libellé et le montant du frais.
 * Si la date est saisie au format français (jj/mm/aaaa), elle est convertie
 * automatiquement au format anglais (aaaa-mm-jj) pour correspondre au format attendu en base.
 *
 * @param string $idVisiteur Identifiant du visiteur
 * @param string $mois Mois au format aaaamm (ex: 202504)
 * @param int    $idFrais Identifiant du frais hors forfait à modifier
 * @param string $date Date du frais (format français ou anglais)
 * @param string $libelle Description du frais
 * @param float  $montant Montant du frais
 *
 * @return void
 */
    public function majFraisHorsForfait($idVisiteur, $mois, $idFrais, $date, $libelle, $montant): void
    {
        if (strpos($date, '/') !== false) {
            $date = Utilitaires::dateFrancaisVersAnglais($date);
        }

        // Préparer la requête SQL pour la mise à jour
        $requetePrepare = $this->connexion->prepare(
            'UPDATE lignefraishorsforfait '
                . 'SET date = :uneDate, libelle = :unLibelle, montant = :unMontant '
                . 'WHERE idvisiteur = :unIdVisiteur '
                . 'AND mois = :unMois '
                . 'AND id = :idFrais'
        );

        // Lier les paramètres à la requête
        $requetePrepare->bindParam(':uneDate', $date, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_INT);

        // Exécuter la requête
        $requetePrepare->execute();
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
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs): void
    {
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
    public function estPremierFraisMois($idVisiteur, $mois): bool
    {
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
    public function dernierMoisSaisi($idVisiteur): string
    {
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
    public function creeNouvellesLignesFrais($idVisiteur, $mois): void
    {
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
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant): void
    {
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
    public function supprimerFraisHorsForfait($idFrais): void
    {
        $requetePrepare = $this->connexion->prepare(
            'DELETE FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Ajoute "REFUSE : " au libellé du frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function refuserFraisHorsForfait($idFrais): void
    {
        $requetePrepare = $this->connexion->prepare(
            'UPDATE lignefraishorsforfait '
                . 'SET libelle = CONCAT("REFUSÉ : ", libelle) '
                . 'WHERE id = :unIdFrais AND libelle NOT LIKE "REFUSÉ : %"'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Reporte un frais hors forfait au mois suivant.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $idFrais ID du frais hors forfait
     * @param String $mois Mois sous la forme aaaamm (mois actuel)
     *
     * @return null
     */
    public function reporterFraisHorsForfait($idVisiteur, $idFrais, $mois): void
    {
        // Calculer le mois suivant
        $annee = (int) substr($mois, 0, 4);
        $moisNum = (int) substr($mois, 4, 2);
        if ($moisNum == 12) {
            $moisNum = 1;
            $annee++;
        } else {
            $moisNum++;
        }
        $moisSuivant = sprintf('%04d%02d', $annee, $moisNum);

        // Vérifier si une fiche de frais existe pour le mois suivant, sinon la créer
        if ($this->estPremierFraisMois($idVisiteur, $moisSuivant)) {
            $this->creeNouvellesLignesFrais($idVisiteur, $moisSuivant);
        }

        // Mettre à jour le frais hors forfait pour le reporter
        $requetePrepare = $this->connexion->prepare(
            'UPDATE lignefraishorsforfait '
                . 'SET mois = :moisSuivant '
                . 'WHERE id = :idFrais AND idvisiteur = :idVisiteur'
        );
        $requetePrepare->bindParam(':moisSuivant', $moisSuivant, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_INT);
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
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
    public function getLesMoisDisponibles($idVisiteur): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponiblesAValider($idVisiteur): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
                . "WHERE fichefrais.idvisiteur = :unIdVisiteur and fichefrais.idetat = 'CL'"
                . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
 * Récupère la liste des mois pour lesquels des fiches de frais ont été validées.
 *
 * Une fiche est considérée comme validée si son état est 'VA'.
 * Cette méthode permet notamment au service comptable de connaître les périodes
 * pour lesquelles les validations ont été effectuées.
 *
 * @return array Tableau associatif contenant les mois (au format aaaamm), 
 *               l'année et le mois séparés ('numAnnee' et 'numMois')
 */
    public function getLesMoisAvecFichesValides(): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT distinct fichefrais.mois AS mois FROM fichefrais '
                . "WHERE fichefrais.idetat = 'VA'"
                . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }

        return $lesMois;
    }

    /**
 * Récupère les visiteurs ayant une fiche de frais validée pour un mois donné.
 *
 * Cette méthode est utile pour savoir quels visiteurs ont des fiches en état 'VA'
 * (validées) pour le mois sélectionné. Elle est souvent utilisée pour générer 
 * les états de paiement ou des exports.
 *
 * @param string $mois Mois recherché au format aaaamm
 * @return array Liste des identifiants des visiteurs ayant une fiche validée
 */
    public function getLesVisiteursAvecFichesValides($mois): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT fichefrais.idvisiteur AS idvisiteur FROM fichefrais '
                . "WHERE fichefrais.idetat = 'VA' AND fichefrais.mois = :unMois "
                . 'ORDER BY fichefrais.idvisiteur ASC'
        );
        $requetePrepare->bindValue(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesVisiteurs = $requetePrepare->fetchAll();
        return $lesVisiteurs;
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
    public function getLesInfosFicheFrais($idVisiteur, $mois): array
    {
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
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
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
    public function majEtatFicheFrais($idVisiteur, $mois, $etat): void
    {
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

    /**
 * Récupère une liste paginée des visiteurs ayant une fiche de frais validée pour un mois donné.
 *
 * Cette méthode est utile pour afficher les visiteurs page par page, par exemple
 * dans une interface web avec pagination (utile si la liste est longue).
 *
 * @param string $mois   Mois concerné au format aaaamm
 * @param int    $limite Nombre de résultats à afficher par page
 * @param int    $offset Position de départ dans la liste (ex : 0, 10, 20...)
 *
 * @return array Liste des identifiants des visiteurs sur la page demandée
 */
    public function getLesVisiteursAvecFichesValidesPagines($mois, $limite, $offset): array
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT idvisiteur 
             FROM fichefrais 
             WHERE mois = :unMois AND idetat = "VA"
             LIMIT :offset, :limite'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        $requetePrepare->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
 * Compte le nombre total de visiteurs ayant une fiche de frais validée pour un mois donné.
 *
 * Cette méthode est souvent utilisée avec la pagination pour savoir combien
 * de pages doivent être générées (en fonction du nombre total de résultats).
 *
 * @param string $mois Mois concerné au format aaaamm
 * @return int Nombre total de visiteurs avec une fiche validée pour ce mois
 */
    public function getNombreVisiteursAvecFichesValides($mois): int
    {
        $requetePrepare = $this->connexion->prepare(
            'SELECT COUNT(idvisiteur) as nombre 
             FROM fichefrais 
             WHERE mois = :unMois AND idetat = "VA"'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return (int) $requetePrepare->fetchColumn();
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
    public function validerFicheFrais($idVisiteur, $mois, $etat, $montant): void
    {
        $requetePrepare = $this->connexion->prepare(
            'UPDATE fichefrais '
                . 'SET idetat = :unEtat, datemodif = now(), montantvalide = :unMontant '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
 * Met à jour l'état d'une fiche de frais pour indiquer que le paiement a été effectué.
 *
 * Cette méthode est utilisée par le service comptable pour marquer une fiche comme "mise en paiement" (état 'MP').
 * Elle met également à jour la date de modification à la date du jour.
 *
 * @param string $idVisiteur Identifiant du visiteur
 * @param string $mois Mois concerné au format aaaamm
 *
 * @return void
 */
    public function validerPaiementVisiteur($idVisiteur, $mois): void
    {
        $requetePrepare = $this->connexion->prepare(
            'UPDATE fichefrais '
                . 'SET idetat = "MP", datemodif = now() '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

/**
 * Calcule le montant total des frais forfaitisés pour un visiteur et un mois donnés.
 * En prenant en compte la puissance du véhicule pour les frais kilométriques.
 *
 * @param string $idVisiteur Identifiant du visiteur
 * @param string $mois Mois concerné au format aaaamm
 * @return float Total des frais forfait pour le mois
 */
public function calculerTotalFraisForfait($idVisiteur, $mois): float
{
    $requetePrepare = $this->connexion->prepare(
        'SELECT fraisforfait.id AS idFrais, quantite, montant 
         FROM lignefraisforfait 
         JOIN fraisforfait ON fraisforfait.id = lignefraisforfait.idfraisforfait
         WHERE idvisiteur = :unIdVisiteur AND mois = :unMois'
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();

    $lignes = $requetePrepare->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;
    foreach ($lignes as $ligne) {
        $total += $ligne['quantite'] * $ligne['montant'];
    }

    return (float)$total;
}


/**
 * Calcule le total des frais hors forfait valides pour un visiteur et un mois donnés.
 *
 * Les frais refusés (ceux dont le libellé commence par "REFUSE :") ne sont pas pris en compte.
 *
 * @param string $idVisiteur Identifiant du visiteur
 * @param string $mois Mois concerné au format aaaamm
 *
 * @return float Montant total des frais hors forfait acceptés
 */
    public function calculerTotalFraisHorsForfait($idVisiteur, $mois): float
    {
        $lesFraisHorsForfait = $this->getLesFraisHorsForfait($idVisiteur, $mois);
        $totalHorsForfait = 0.0;

        foreach ($lesFraisHorsForfait as $frais) {
            // Exclure les frais dont le libellé commence par "REFUSE :"
            if (isset($frais['montant']) && strpos($frais['libelle'], 'REFUSE :') !== 0) {
                $totalHorsForfait += (float)$frais['montant'];
            }
        }

        return $totalHorsForfait;
    }



/**
 * Calcule le montant total validé d'une fiche de frais (forfait + hors forfait).
 * Prend en compte le tarif véhicule dynamique pour les frais kilométriques.
 *
 * @param string $idVisiteur Identifiant du visiteur
 * @param string $mois Mois concerné au format aaaamm
 * @return float Montant total validé à rembourser
 */
public function calculerMontantValide($idVisiteur, $mois): float
{
    // 1. Frais forfaitisés (avec tarif véhicule)
    $totalForfait = $this->calculerTotalFraisForfait($idVisiteur, $mois);

    // 2. Frais hors forfait (hors REFUSE)
    $requeteHorsForfait = $this->connexion->prepare(
        'SELECT SUM(montant) AS totalHorsForfait 
         FROM lignefraishorsforfait
         WHERE idvisiteur = :unIdVisiteur AND mois = :unMois 
         AND libelle NOT LIKE "REFUSE : %"'
    );
    $requeteHorsForfait->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requeteHorsForfait->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requeteHorsForfait->execute();
    $totalHorsForfait = $requeteHorsForfait->fetchColumn();

    return (float)$totalForfait + (float)$totalHorsForfait;
}
}
