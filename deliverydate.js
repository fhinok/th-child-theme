jQuery(function ($) {
    $(document).ready(function() {

        now = new Date()
        tomorrow = new Date(now)
        tomorrow.setDate(tomorrow.getDate() + 1)

        function nextAllowed(date){
            date = new Date(date)
            day = date.getDay()
            hour = date.getHours()

            if(date.getDay() === 0){
                date.setDate(date.getDate() + 1)
                return date
            }
            if(date.getDay() === 6){
                date.setDate(date.getDate() + 2)
                return date
            }
            if(date.getDay() === 1){
                return date
            }
            if(hour >= 15){
                if (1 < day && day < 5) {
                    date.setDate(date.getDate() + 1)
                    return date
                } else {
                    return nextAllowed(date.setDate(date.getDate() + 1))
                }
            }
            return date
        }

        $('#shipping_date').datepicker({
            offset: 6,
            language: 'de-De',
            format: 'dd.mm.YYYY',
            date: nextAllowed(tomorrow),
            startDate: new Date(now),
            weekStart: 1,
            autoPick: true,
            filter: (date, view) => {
                if(date.getDay() === 0 && view === 'day' || date.getDay() === 6 && view === 'day') {
                    return false;
                }
            }
        })

        $('#shipping_date').on('pick.datepicker', function (e) {
            picked_date = new Date(e.date)
            now = new Date(now)

            $('.sameday').remove();
            $('#place_order').prop("disabled",false);

            if(picked_date.getDate() === now.getDate()) {
                text = "Für heute kann keine Lieferung mehr über den Shop bestellt werden."
                $('#shipping_date').closest('.form-row').append("<p class='sameday'>" + text + "</p>");
                $('#place_order').prop("disabled",true);
                return
            }

            if(picked_date.valueOf() < nextAllowed(now).valueOf() && picked_date.getDay() != 1){
                text = "Achtung: Bestellungen nach 15:00 Uhr können nicht für den Folgetag gewährleistet werden."
                $('#shipping_date').closest('.form-row').append("<p class='sameday'>" + text + "</p>");
                // $('#place_order').prop("disabled",true);
            }
        });

    });
});