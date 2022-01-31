jQuery(document).ready(function ($) {
  $('.kbi_calc_price_week_to_day').on('input', function () {
    var price = $(this).val()
    var day_price = price / 7
    $('.kbi_calc_price_day_to_week').val(day_price.toFixed(4))
  })

  $('.kbi_calc_price_day_to_week').on('input', function () {
    var price = $(this).val()
    var day_price = price * 7
    $('.kbi_calc_price_week_to_day').val(day_price.toFixed(0))
  })
});

