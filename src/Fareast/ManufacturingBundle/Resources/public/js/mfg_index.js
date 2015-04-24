jQuery(document).ready(function() {

    $('#datepaginator').datepaginator({
        onSelectedDateChanged: function(event, date) 
        {    
            date_picked = moment(date).format('YYYYMMDD'); 
        
            $("#edit_button").attr("href", "/manufacturing/daily-consumption/" + date_picked);
            $("#print_button").attr("href", "/manufacturing/production-calendar/pdf/" + date_picked);

            $.getJSON('/manufacturing/production-calendar/ajax/' + date_picked, function(data) 
            {
                $('#cform-mollases').val(data.balance_mollases);
                $('#cform-bunker').val(data.balance_bunker);
                $('#cform-acid').val(data.balance_acid);
                $('#cform-soda').val(data.balance_soda);
                $('#cform-urea').val(data.balance_urea);
                $('#cform-salt').val(data.balance_salt);
                $('#shift_reports').empty();

                var ids = data.shift_reports_id.toString().split(',');
                var fermentations = data.shift_reports_fermentations.toString().split(',');
                var biogas = data.shift_reports_biogas.toString().split(',');
                var bunker = data.shift_reports_bunker.toString().split(',');
                var shift = data.shift_reports_shift.toString().split(',');
                var user = data.shift_reports_user.toString().split(',');
                var checker = false;
                var append;
                var id;

                // 1st
                for (i = 0; i < ids.length; i++) 
                { 
                    if (shift[i] == "7:00 to 15:00")
                    {
                        checker = true;
                        id = i;
                    }
                }

                if (checker == true)
                {
                    
                    append = '<div class="col-sm-6 col-md-4">';
                    append += '<div class="thumbnail">';
                    append += '<div class="caption">'; 
                    append += '<h3 style="font-weight: bold;">'+ user[id] +'</h3>';

                    append += '<h4 class="form-section" style="margin-top: 10px;">Supervisor-in-charge<br>1st Shift - 7:00 to 15:00</h4>'; 
                    append += '<div class="form-horizontal">';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Fermentation</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Fermentation" value="'+ fermentations[id] +'" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Biogas</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Biogas" value="'+ biogas[id] +'" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Bunker Fuel/Steam</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Bunker Fuel/Steam" value="'+ bunker[id] +'" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '</div>';
                    append += '<div style="text-align: right;">';
                    append += '<a href="/manufacturing/shift-report/edit/'+ ids[id] +'/7:00 to 15:00" class="btn blue">View</a>';
                    append += '</div></div></div></div>';

                }
                else
                {
                    append = '<div class="col-sm-6 col-md-4">';
                    append += '<div class="thumbnail">';
                    append += '<div class="caption">'; 
                    append += '<h3 style="font-weight: bold;">N/A</h3>';
                    append += '<h4 class="form-section" style="margin-top: 10px;">Supervisor-in-charge<br>1st Shift - 7:00 to 15:00</h4>'; 
                    append += '<div class="form-horizontal">';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Fermentation</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Fermentation" value="N/A" readonly="readonly">';

                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Biogas</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Biogas" value="N/A" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Bunker Fuel/Steam</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Bunker Fuel/Steam" value="N/A" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '</div>';
                    append += '<div style="text-align: right;">';
                    append += '<a href="/manufacturing/shift-report/' + date_picked + '/7:00 to 15:00" class="btn blue">Create Shift Report</a>';
                    append += '</div></div></div></div>';
                }

                $('#shift_reports').append(append);



                // 2nd
                checker = false;
                for (i = 0; i < ids.length; i++) 
                { 
                    if (shift[i] == "15:00 to 23:00")
                    {
                        checker = true;
                        id = i;
                    }
                }

                if (checker == true)
                {
                    
                    append = '<div class="col-sm-6 col-md-4">';
                    append += '<div class="thumbnail">';
                    append += '<div class="caption">'; 
                    append += '<h3 style="font-weight: bold;">'+ user[id] +'</h3>';

                    append += '<h4 class="form-section" style="margin-top: 10px;">Supervisor-in-charge<br>2nd Shift - 15:00 to 23:00</h4>'; 
                    append += '<div class="form-horizontal">';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Fermentation</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Fermentation" value="'+ fermentations[id] +'" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Biogas</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Biogas" value="'+ biogas[id] +'" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Bunker Fuel/Steam</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Bunker Fuel/Steam" value="'+ bunker[id] +'" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '</div>';
                    append += '<div style="text-align: right;">';
                    append += '<a href="/manufacturing/shift-report/edit/'+ ids[id] +'/15:00 to 23:00" class="btn blue">View</a>';
                    append += '</div></div></div></div>';

                }
                else
                {
                    append = '<div class="col-sm-6 col-md-4">';
                    append += '<div class="thumbnail">';
                    append += '<div class="caption">'; 
                    append += '<h3 style="font-weight: bold;">N/A</h3>';
                    append += '<h4 class="form-section" style="margin-top: 10px;">Supervisor-in-charge<br>2nd Shift - 15:00 to 23:00</h4>'; 
                    append += '<div class="form-horizontal">';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Fermentation</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Fermentation" value="N/A" readonly="readonly">';

                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Biogas</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Biogas" value="N/A" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Bunker Fuel/Steam</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Bunker Fuel/Steam" value="N/A" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '</div>';
                    append += '<div style="text-align: right;">';
                    append += '<a href="/manufacturing/shift-report/' + date_picked + '/15:00 to 23:00" class="btn blue">Create Shift Report</a>';
                    append += '</div></div></div></div>';
                }

                $('#shift_reports').append(append);




                // 3rd
                checker = false;
                for (i = 0; i < ids.length; i++) 
                { 
                    if (shift[i] == "23:00 to 7:00")
                    {
                        checker = true;
                        id = i;
                    }
                }

                if (checker == true)
                {
                    
                    append = '<div class="col-sm-6 col-md-4">';
                    append += '<div class="thumbnail">';
                    append += '<div class="caption">'; 
                    append += '<h3 style="font-weight: bold;">'+ user[id] +'</h3>';

                    append += '<h4 class="form-section" style="margin-top: 10px;">Supervisor-in-charge<br>3rd Shift - 23:00 to 7:00</h4>'; 
                    append += '<div class="form-horizontal">';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Fermentation</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Fermentation" value="'+ fermentations[id] +'" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Biogas</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Biogas" value="'+ biogas[id] +'" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Bunker Fuel/Steam</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Bunker Fuel/Steam" value="'+ bunker[id] +'" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '</div>';
                    append += '<div style="text-align: right;">';
                    append += '<a href="/manufacturing/shift-report/edit/'+ ids[id] +'/23:00 to 7:00" class="btn blue">View</a>';
                    append += '</div></div></div></div>';

                }
                else
                {
                    append = '<div class="col-sm-6 col-md-4">';
                    append += '<div class="thumbnail">';
                    append += '<div class="caption">'; 
                    append += '<h3 style="font-weight: bold;">N/A</h3>';
                    append += '<h4 class="form-section" style="margin-top: 10px;">Supervisor-in-charge<br>3rd Shift - 23:00 to 7:00</h4>'; 
                    append += '<div class="form-horizontal">';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Fermentation</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Fermentation" value="N/A" readonly="readonly">';

                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Biogas</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Biogas" value="N/A" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '<div id="cgroup-sku" class="form-group">';
                    append += '<label class="col-md-5 control-label">Bunker Fuel/Steam</label>';
                    append += '<div class="col-md-7">';
                    append += '<input id="name" name="name" class="form-control " placeholder="Bunker Fuel/Steam" value="N/A" readonly="readonly">';
                    append += '</div>';
                    append += '</div>';
                    append += '</div>';
                    append += '<div style="text-align: right;">';
                    append += '<a href="/manufacturing/shift-report/' + date_picked + '/23:00 to 7:00" class="btn blue">Create Shift Report</a>';
                    append += '</div></div></div></div>';
                }

                $('#shift_reports').append(append);

            });

        }
    });


});   