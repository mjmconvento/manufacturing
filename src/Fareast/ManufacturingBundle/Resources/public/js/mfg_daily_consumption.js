
function updateMolRunningBalance()
{
    mol_beginning_balance = $('#cform-begin-mol').val();
    mol_purchases = $('#cform-purchase').val();
    mol_pumped_mdt = $('#cform-pumped').val();
    total = ( parseInt(mol_beginning_balance) + parseInt(mol_purchases) ) - parseInt(mol_pumped_mdt);
    $('#cform-run-mol').val(total);
}

function updateBunkerRunningBalance()
{
    bunker_beginning_balance = $('#cform-begin-bunk').val();
    bunker_purchases = $('#cform-bunk-pur').val();
    bunker_consumed = $('#cform-consumed').val();

    total = ( parseInt(bunker_beginning_balance) + parseInt(bunker_purchases) ) - parseInt(bunker_consumed);
    $('#cform-run-bunk').val(total);
}

function updateSulRunningBalance()
{
    sul_beginning_balance = $('#cform-begin-acid').val();
    sul_purchases = $('#cform-acid-purchase').val();
    sul_consumed = $('#cform-acid-consumed').val();

    total = ( parseInt(sul_beginning_balance) + parseInt(sul_purchases) ) - parseInt(sul_consumed);
    $('#cform-run-acid').val(total);
}

function updateSodaRunningBalance()
{
    soda_beginning_balance = $('#cform-begin-soda').val();
    soda_purchases = $('#cform-soda-purchase').val();
    soda_consumed = $('#cform-soda-consumed').val();

    total = ( parseInt(soda_beginning_balance) + parseInt(soda_purchases) ) - parseInt(soda_consumed);
    $('#cform-run-soda').val(total);
}

function updateUreaRunningBalance()
{
    urea_beginning_balance = $('#cform-begin-urea').val();
    urea_purchases = $('#cform-urea-purchase').val();
    urea_consumed = $('#cform-urea-consumed').val();

    total = ( parseInt(urea_beginning_balance) + parseInt(urea_purchases) ) - parseInt(urea_consumed);
    $('#cform-run-urea').val(total);
}

function updateSaltRunningBalance()
{
    salt_beginning_balance = $('#cform-begin-salt').val();
    salt_purchases = $('#cform-salt-purchase').val();
    salt_consumed = $('#cform-salt-consumed').val();

    total = ( parseInt(salt_beginning_balance) + parseInt(salt_purchases) ) - parseInt(salt_consumed);
    $('#cform-run-salt').val(total);
}

function updateElectricityUsed( electricity_final, electricity_beginning )
{        
    electricity_final = $('#cform-electricity-final').val();
    electricity_beginning = $('#cform-electricity-beginning').val();
    total = parseInt(electricity_final) - parseInt(electricity_beginning);
    $('#cform-electricity-used').val(total);

    electricity_used = $('#cform-electricity-used').val();
    $('#cform-alcohol-kw').val(parseInt(electricity_used) * 350);
}


jQuery(document).ready(function() {       
    ComponentsPickers.init();

    $( '#cform-begin-mol, #cform-purchase, #cform-pumped' ).change(function() {
        updateMolRunningBalance();
    });

    $( '#cform-begin-bunk, #cform-bunk-pur, #cform-consumed' ).change(function() {
        updateBunkerRunningBalance();
    });

    $( '#cform-begin-acid, #cform-acid-purchase, #cform-acid-consumed' ).change(function() {
        updateSulRunningBalance();
    });

    $( '#cform-begin-soda, #cform-soda-purchase, #cform-soda-consumed' ).change(function() {
        updateSodaRunningBalance();
    });

    $( '#cform-begin-soda, #cform-soda-purchase, #cform-soda-consumed' ).change(function() {
        updateUreaRunningBalance();
    });

    $('#cform-begin-urea, #cform-urea-purchase, #cform-urea-consumed').change(function() {
        updateUreaRunningBalance();
    });

    $('#cform-begin-salt, #cform-salt-purchase, #cform-salt-consumed').change(function() {
        updateSaltRunningBalance();
    });

    $('#cform-electricity-final, #cform-electricity-beginning').change(function() {
        updateElectricityUsed();
    });

});  
