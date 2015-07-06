<h2>Riepilogo Ordine</h2>
<form action="cliente/conferma_ordine" method="post">
    
 <?    
    $ordine = OrdineFactory::instance()->getOrdine($ordineId);
    $RigaOrdines = RigaOrdineFactory::instance()->getRigaOrdinePerIdOrdine($ordine);
    
    if($ordine->getSpedizione() == "si") $spedizioneSi = true;
    else $spedizioneSi = false;
    

?>  
    <input type="hidden" name="ordineId" value="<?= $ordine->getId() ?>" />
    <table>

            <tr>
                <th>Prodotto</th>
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
                <th>Fascia oraria</th> 
                <th>Domicilio</th>
                <th>Prezzo Domicilio</th>                
                <th>Prezzo Pizze</th>
                <th>Prezzo Totale</th>                     
            </tr>       
            <tr>
                <td><?= OrdineFactory::instance()->getValoreOrario($ordine->getOrario()) ?></td>
                <td><? if($spedizioneSi){?>si<? } else {?>no<? } ?></td>            
                <td><? if($spedizioneSi){?>1.5<? } else {?>0<? } ?></td>
                <td><?= RigaOrdineFactory::instance()->getPrezzoParziale($ordine) . "€ " ?></td>                 
                <td><?= OrdineFactory::instance()->getPrezzoTotale($ordine) . "€ " ?></td>                 
            </tr>
    </table>        
    <button type="submit" name="cmd" value="conferma_ordine">Conferma</button>
    <button type="submit" name="cmd" value="cancella_ordine">Cancella</button>
    </form>
