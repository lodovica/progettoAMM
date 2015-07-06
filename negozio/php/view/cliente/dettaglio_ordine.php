<h2>Dettaglio ordine n°<?=$ordine->getId()?> del <?=substr($ordine->getData(),0,10)?></h2>

    <table>

            <tr>
                <th >Prodotto</th>
                <th>Dimensione</th>                
                <th>Quantita</th>
                <th>Prezzo</th>      
                <th>Prezzo TOT</th>                 
            </tr>     

    <?foreach ($RigaOrdines as $RigaOrdine) {
            $prodotto = ProdottoFactory::instance()->getProdottoPerId($RigaOrdine->getProdotto());?>
            <tr>
                <td><?= $prodotto->getNome()?></td>
                <td><?= $RigaOrdine->getDimensione() ?></td>
                <td><?= $RigaOrdine->getQuantita() ?></td>                
                <td><?= (RigaOrdineFactory::instance()->getPrezzoPerProdotti($RigaOrdine)/$RigaOrdine->getQuantita()) . "€ "?></td>
                <td><?= RigaOrdineFactory::instance()->getPrezzoPerProdotti($RigaOrdine) . "€ "?></td>                               
                   
            </tr>
    <? } ?>    
             <tr>
                <th>Fascia oraria*</th>                  
                <th>Domicilio</th>
                <th>Prezzo Domicilio</th>                
                <th>Prezzo Pizze</th>
                <th>Prezzo Totale</th>                     
            </tr>       
            <tr>
                <td><?= OrdineFactory::instance()->getValoreOrario($ordine->getOrario()) ?></td>           
                <td><? if($ordine->getSpedizione() == "si"){?>si<? } else {?>no<? } ?></td>            
                <td><? if($ordine->getSpedizione() == "si"){?>1.5€<? } else {?>0€<? } ?></td>
                <td><?= RigaOrdineFactory::instance()->getPrezzoParziale($ordine) . "€ "?></td>                 
                <td><?= OrdineFactory::instance()->getPrezzoTotale($ordine) . "€ "?></td>                 
            </tr>
    </table>


