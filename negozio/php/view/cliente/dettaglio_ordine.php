<h2>Dettaglio ordine nr. <?=$ordine->getId()?> del <?=substr($ordine->getData(),0,10)?></h2>

    <table>

            <tr>
                <th>Prodotto</th>
                <th>Dimensione</th>                
                <th>Quantita'</th>
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
                <th>Spedizione</th>
                <th>Prezzo spedizione</th>                
                <th>Prezzo Prodotti</th>
                <th>Prezzo Totale</th>                     
            </tr>       
            <tr>          
                <td><? if($ordine->getSpedizione() == "si"){?>si<? } else {?>no<? } ?></td>            
                <td><? if($ordine->getSpedizione() == "si"){?>1.5€<? } else {?>0€<? } ?></td>
                <td><?= RigaOrdineFactory::instance()->getPrezzoParziale($ordine) . "€ "?></td>                 
                <td><?= OrdineFactory::instance()->getPrezzoTotale($ordine) . "€ "?></td>                 
            </tr>
    </table>


