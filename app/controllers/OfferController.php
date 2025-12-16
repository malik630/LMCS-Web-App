<?php

class OfferController extends Controller
{
    public function index()
    {
        $offerModel = $this->model('Offer');
        $offers = $offerModel->getAll();
        $groupedOffers = [
            'stage' => [],
            'these' => [],
            'bourse' => [],
            'collaboration' => [],
            'emploi' => [],
            'autre' => []
        ];
        
        foreach ($offers as $offer) {
            $type = $offer['type'];
            if (isset($groupedOffers[$type])) {
                $groupedOffers[$type][] = $offer;
            }
        }
        
        $data = [
            'offers' => $offers,
            'groupedOffers' => $groupedOffers,
            'totalOffers' => count($offers)
        ];
        
        $this->view('OfferView', $data);
    }
}
?>