$(
function() {

//----------------------
//- HOVER TABLE LOGIC -
//----------------------
  var consultant_tasks_location = $('[id^=consultants_tasks]').DataTable({
        "paging" : true,
        "bFilter" : false,
        "iDisplayLength": 5,
        "lengthChange": false,
        "order" : [[ 1, "desc" ]]
        });

//-----------------------
//- END HOVER TABLE
//-----------------------


}
);
