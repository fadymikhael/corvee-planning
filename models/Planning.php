<?php
class Planning
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function getPlanningByYear($annee)
    {
        return $this->db->planning->findOne(['annee' => (int)$annee]);
    }

    public function updatePlanning($annee, $semaine, $responsable)
    {
        try {
            // Récupérer le planning existant ou créer un nouveau
            $planning = $this->getPlanningByYear($annee);
            if (!$planning) {
                $planning = ['annee' => $annee, 'semaines' => array_fill(0, 52, ['responsable' => ''])];
            }

            // Mettre à jour la semaine spécifique
            $index = $semaine - 1;
            $planning['semaines'][$index]['responsable'] = $responsable;

            // Sauvegarder dans MongoDB
            return $this->db->planning->updateOne(
                ['annee' => (int)$annee],
                ['$set' => ['semaines' => $planning['semaines']]],
                ['upsert' => true]
            );
        } catch (Exception $e) {
            error_log("Erreur dans updatePlanning: " . $e->getMessage());
            return false;
        }
    }
    public function resetResponsable($username)
    {
        try {
            $this->db->planning->updateMany(
                [
                    'semaines.responsable' => $username
                ],
                [
                    '$set' => ['semaines.$[elem].responsable' => 'personne']
                ],
                [
                    'arrayFilters' => [['elem.responsable' => $username]]
                ]
            );
        } catch (Exception $e) {
            error_log("Erreur lors de la réinitialisation des semaines : " . $e->getMessage());
        }
    }


    public function getStats($annee)
    {
        try {
            $pipeline = [
                ['$match' => ['annee' => (int)$annee]],
                ['$unwind' => [
                    'path' => '$semaines',
                    'preserveNullAndEmptyArrays' => true
                ]],
                ['$group' => [
                    '_id' => '$semaines.responsable',
                    'total_semaines' => ['$sum' => 1]
                ]],
                ['$match' => [
                    '_id' => ['$ne' => null]
                ]]
            ];

            return $this->db->planning->aggregate($pipeline)->toArray();
        } catch (Exception $e) {
            error_log("Erreur dans getStats : " . $e->getMessage());
            return [];
        }
    }
}
