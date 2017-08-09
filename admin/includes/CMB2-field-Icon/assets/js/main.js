(function ($) {
  $('.cmb-type-icon ul li').click(function(){
    var jCMBTypeIcon = $(this).closest('.cmb-type-icon');
    // Set selected styles
    jCMBTypeIcon.find('li').removeClass('selected').addClass('unselected');
    $(this).removeClass('unselected').addClass('selected');
    // Tick the input
    jCMBTypeIcon.find('input').removeAttr('checked');
    $(this).find('input').attr('checked', '1');
  })
  
  $('.cmb-type-icon ul.icon-scroll').click(function(){
    // Important to disable the custom input 
    // because otherwise it will override the radio selection
    var jCMBTypeIcon = $(this).closest('.cmb-type-icon');
    jCMBTypeIcon.find('.custom_icon input').attr('disabled', '1');
  });
  
  $('.cmb-type-icon .custom_icon').click(function(){
    var jCMBTypeIcon = $(this).find('input').removeAttr('disabled').focus();
  });

  $(document).ready(function(){
    $('.cmb-type-icon .selected').each(function(){
      if (this.scrollIntoView) this.scrollIntoView();
    });
  });
})(jQuery);
