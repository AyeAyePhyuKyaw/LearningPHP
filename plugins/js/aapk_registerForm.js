jQuery(function($) {
  $('#search_div').hide();
  $('#result_header').hide();
  $('#result_table').hide();
  $('#reg_form').css('background-image', 'url(/wordpress/wp-content/plugins/aapk_contactform/img/normal.jpg)');

  $('#show_btn').on('click', function() {
    $('#search_div').slideDown(600);
    $('#result_header').slideDown(600);
    $('#result_table').slideDown(600);
    $('#reg_form').css('background-image', 'url(/wordpress/wp-content/plugins/aapk_contactform/img/show1.jpg)');
  });

  $('#hide_btn').on('click', function() {
    $('#search_div').slideUp(600);
    $('#result_header').slideUp(600);
    $('#result_table').slideUp(600);
    $('#reg_form').css('background-image', 'url(/wordpress/wp-content/plugins/aapk_contactform/img/hide.jpg)');
  });

  $('#search_box').on('input', function(e) {
    var userSearch = $(this).val();
    var data = {
      action: 'search_data_action',
      searchData: userSearch,
    };
    $.ajax({
      url: ajax_object.ajax_url,
      method: 'post',
      data: data,
      dataType: 'json'
    }).done(function(response) {
      if (response && $.isArray(response)) {
        createTableByJqueryEach(response);
      }
    });
  });

  function createTableByJqueryEach(data) {
    var searchTable = $('#search_table tbody');
    searchTable.empty();
    $.each(data, function() {
      var tr = $('<tr/>');
      tr.append($('<td/>').text(this.Name));
      tr.append($('<td/>').text(this.Phone_No));
      tr.append($('<td/>').text(this.Age));
      tr.append($('<td/>').text(this.Occupation));
      searchTable.append(tr);
    });
  }
});