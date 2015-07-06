<h2>Lista Pizze</h2>

<div class="input-form">

<table>
    <tr>
        <th>Nome</th>
        <th>Ingredienti</th>    
        <th>Normali</th>
        <th>Prezzo</th>         
        <th>Giganti</th>
        <th>Prezzo</th>         
    </tr>
    
<form action="cliente/ordina" method="post">
    

    <?foreach ($prodotti as $prodotto) {
        ?>
        <tr>
            <td class="col-small"><?= $prodotto->getNome() ?></td>
            <td class="col-large"><?= $prodotto->getDescrizione() ?></td>
            <td class="col-small"><input type="number" name=<?= $prodotto->getId()."medio" ?> maxlength="2" size="2" min="0" max="10"></td>            
            <td class="col-small"><?= $prodotto->getPrezzo() . "€ "?></td>
            <td class="col-small"><input type="number" name=<?= $prodotto->getId()."grande" ?> maxlength="2" size="2" min="0" max="10"></td>
            <td class="col-small"><?= $prodotto->getPrezzo()+($prodotto->getPrezzo()*30/100) . "€ "?></td>            
        </tr>
    <? } ?>
</table>

    <label for="domicilio">Consegna a domicilio*</label>
        <select name="domicilio" id="domicilio">
                <option value="si">si</option>
                <option value="no">no</option>
        </select>  
    <label for="orario">Fascia oraria</label>
        <select name="orario" id="orario">
<!--il seguente ciclo esegue dei controlli per capire quali fasce orarie mostrare a seconda dell'orario attuale. 
Ovviamente nel caso una delle fasce orarie fosse precedente all'orario attuale questa non viene visualizzata-->            
    <?foreach ($orari as $orario) {
               $fasciaOraria = $orario->getFasciaOraria();
               $ora = intval(substr($fasciaOraria, 0, 2));
               $minuti = intval(substr($fasciaOraria, 3, 5));
               if(intval(Date("H")) == $ora){ 
                if(intval(Date("i")) < $minuti){?>
                   <option value="<?= $orario->getId() ?>"><?= $fasciaOraria ?></option>
                
            <?  }
               }elseif(intval(Date("H")) < $ora){?>
                   <option value="<?= $orario->getId() ?>"><?= $fasciaOraria ?></option>
            <?       
               }
               } ?>        
                   
    </select>     
    <button type="submit" name="cmd" value="procedi_ordine">Procedi</button>

    
</div> 
    
