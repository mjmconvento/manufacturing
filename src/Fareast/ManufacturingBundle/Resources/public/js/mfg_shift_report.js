function updateAlcoholProduced()
{
    fine_alcohol = $('#cform-fine-alcohol').val();
    heads_alcohol = $('#cform-heads-alcohol').val();

    total = ( parseInt(heads_alcohol) / parseInt(fine_alcohol) );
    $('#cform-produced').val(total);
}

function updateGPLA()
{
    biogas = $('#cform-biogas').val();
    fine_alcohol = $('#cform-fine-alcohol').val();

    total = ( parseInt(biogas) / parseInt(fine_alcohol) );
    $('#cform-gpla').val(total.toFixed(2));

    biogas_bunker = biogas * .4;
    biogas_steam = biogas * 11;
    $('#cform-biogas-bunker').val(biogas_bunker.toFixed(2));
    $('#cform-steam').val(biogas_steam);

}

function updateLOALOB()
{
    loa_lob = $('#cform-loa-lob').val();
    fine_alcohol = $('#cform-fine-alcohol').val();

    total = ( parseInt(fine_alcohol) / parseInt(loa_lob) );
    $('#cform-bunker').val(total.toFixed(2));
}

function updateSteamProduced()
{
    produced_steam = $('#cform-produced-steam').val();
    form_steam = $('#cform-steam').val();

    total = ( parseInt(produced_steam) - parseInt(form_steam) );
    $('#cform-steam-produced').val(total.toFixed(2));
}

function updateLOAHusk()
{
    form_husk = $('#cform-husk').val();
    fine_alcohol = $('#cform-fine-alcohol').val();

    total = ( parseInt(fine_alcohol) / parseInt(form_husk) );
    $('#cform-loa-husk').val(total.toFixed(2));

    total2 = ( parseInt(form_husk) / parseInt(fine_alcohol) );
    $('#cform-husk-loa').val(total2.toFixed(2));

}

function updateSteamLOA()
{
    produced_steam = $('#cform-produced-steam').val();
    fine_alcohol = $('#cform-fine-alcohol').val();

    total = ( parseInt(produced_steam) / parseInt(fine_alcohol) );
    $('#cform-steam-loa').val(total.toFixed(2));

}

function updateSteamHusk()
{
    produced_steam = $('#cform-steam-produced').val();
    husk = $('#cform-husk').val();

    total = ( parseInt(produced_steam) / parseInt(husk) );
    $('#cform-steam-husk').val(total.toFixed(2));

}


jQuery(document).ready(function() {       
    ComponentsPickers.init();

    $( '#cform-fine-alcohol, #cform-heads-alcohol' ).change(function() {
        updateAlcoholProduced();
    });

    $( '#cform-biogas, #cform-fine-alcohol' ).change(function() {
        updateGPLA();
    });

    $( '#cform-loa-lob, #cform-fine-alcohol' ).change(function() {
        updateLOALOB();
    });

    $( '#cform-produced-steam, #cform-steam' ).change(function() {
        updateSteamProduced();
    });

    $( '#cform-husk, #cform-fine-alcohol' ).change(function() {
        updateLOAHusk();
    });

    $( '#cform-produced-steam, #cform-fine-alcohol' ).change(function() {
        updateSteamLOA();
    });

    $( '#cform-steam-produced, #cform-husk' ).change(function() {
        updateSteamHusk();
    });

});   