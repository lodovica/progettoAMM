<h2>Lista Prodotti</h2>

<div class="input-form">

<table>
    <tr>
        <th>Nome</th>
        <th>Descrizione</th>    
        <th>Barattolo Medio</th>
        <th>Prezzo</th>         
        <th>Barattolo Grande</th>
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

    <label for="spedizione">Spedizione a domicilio (altrimenti ritiro in negozio)</label>
        <select name="spedizione" id="spedizione">
                <option value="si">si</option>
                <option value="no">no</option>
        </select>         
   
    <button type="submit" name="cmd" value="procedi_ordine">Procedi</button>

    
</div> 
    
