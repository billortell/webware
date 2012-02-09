
function hww_config_app() {
  $('#hww_config_layout_body').load('/hww/config-app-index');
}
function hww_config_app_list() {
  $('#hww_config_app_content').load('/hww/config-app-list');
}
function hww_config_app_view(qs) {
  $('#hww_config_app_content').load('/hww/config-app-view?appid='+qs);
}
function hww_config_app_setup(qs) {
  $('#hww_config_app_content').load('/hww/config-app-setup?appid='+qs);
}
function hww_config_app_instance_setup(qs) {
  $('#hww_config_app_content').load('/hww/config-app-instance-setup?appid='+qs);
}
function hww_config_app_instance() {
  $('#hww_config_app_content').load('/hww/config-app-instance');
}
function hww_config_dataconn() {
  $('#hww_config_layout_body').load('/hww/config-dataconn');
  /** $.ajax({ 
    type: "GET",
    url: "/hww/config-dataconn",
    data: "",
    success: function(data) {
      $("#hww_config_layout_body").empty().append(data);
    }
  }); */
}
