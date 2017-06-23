define([
    'zjs/z',
    'zjs/z.selector'
], function (z, s) {
    'use strict';

    var init = function () {
        var options = {
            YearSelector: '#sel_year',
            MonthSelector: '#sel_month',
            DaySelector: '#sel_day',
            YearFirstText: z.trans('Year'),
            MonthFirstText: z.trans('Month'),
            DayFirstText: z.trans('Day'),
            FirstYearValue: s('#sel_year')[0].dataset.value,
            FirstMonthValue: s('#sel_month')[0].dataset.value,
            FirstDayValue: s('#sel_day')[0].dataset.value
        }
        var dataPicker = new DatePicker(options);

        if (options.FirstYearValue && options.FirstMonthValue)
            dataPicker.BuildDay();
    };

    function DatePicker(options) {
        var defaults = {
            YearSelector: '#sel_year',
            MonthSelector: '#sel_month',
            DaySelector: '#sel_day',
            YearFirstText: '--',
            MonthFirstText: '--',
            DayFirstText: '--',
            FirstYearValue: 0,
            FirstMonthValue: 0,
            FirstDayValue: 0
        };

        defaults.YearSelector = options.YearSelector || defaults.YearSelector;
        defaults.MonthSelector = options.MonthSelector || defaults.MonthSelector;
        defaults.DaySelector = options.DaySelector || defaults.DaySelector;
        defaults.YearFirstText = options.YearFirstText || defaults.YearFirstText;
        defaults.MonthFirstText = options.MonthFirstText || defaults.MonthFirstText;
        defaults.DayFirstText = options.DayFirstText || defaults.DayFirstText;
        defaults.FirstYearValue = options.FirstYearValue ? options.FirstYearValue : 0;
        defaults.FirstMonthValue = options.FirstMonthValue ? options.FirstMonthValue : 0;
        defaults.FirstDayValue = options.FirstDayValue ? options.FirstDayValue : 0;

        var $YearSelector = s(defaults.YearSelector);
        var $MonthSelector = s(defaults.MonthSelector);
        var $DaySelector = s(defaults.DaySelector);


        $YearSelector.html("<option value=\"" + defaults.FirstYearValue + "\">" + defaults.YearFirstText + "</option>");
        $MonthSelector.html("<option value=\"" + defaults.FirstMonthValue + "\">" + defaults.MonthFirstText + "</option>");
        $DaySelector.html("<option value=\"" + defaults.FirstDayValue + "\">" + defaults.DayFirstText + "</option>");

        var yearNow = new Date().getFullYear();


        var yearOptions = document.createDocumentFragment();
        for (var i = yearNow; i >= 1900; i--) {
            var yearOption = document.createElement('option');
            yearOption.value = yearOption.innerHTML = i;
            if (i == defaults.FirstYearValue) {
                yearOption.setAttribute('selected', 'true');
            }
            yearOptions.appendChild(yearOption);
        }

        $YearSelector.append(yearOptions);

        for (var j = 1; j <= 12; j++) {
            var monthOption = document.createElement('option');
            monthOption.value = monthOption.innerHTML = j;
            if (j == defaults.FirstMonthValue) {
                monthOption.setAttribute('selected', 'true');
            }
            $MonthSelector.append(monthOption);
        }

        function BuildDay() {
            if ($YearSelector.prop('value') == 0 || $MonthSelector.prop('value') == 0) {
                $DaySelector.html("<option value=\"" + defaults.FirstDayValue + "\">" + defaults.DayFirstText + "</option>");
            } else {
                $DaySelector.html("<option value=\"" + defaults.FirstDayValue + "\">" + defaults.DayFirstText + "</option>");
                var year = parseInt($YearSelector.prop('value'));
                var month = parseInt($MonthSelector.prop('value'));
                var dayCount = 0;
                switch (month) {
                    case 1:
                    case 3:
                    case 5:
                    case 7:
                    case 8:
                    case 10:
                    case 12:
                        dayCount = 31;
                        break;
                    case 4:
                    case 6:
                    case 9:
                    case 11:
                        dayCount = 30;
                        break;
                    case 2:
                        dayCount = 28;
                        if ((year % 4 == 0) && (year % 100 != 0) || (year % 400 == 0)) {
                            dayCount = 29;
                        }
                        break;
                    default:
                        break;
                }

                for (var k = 1; k <= dayCount; k++) {
                    var dayOption = document.createElement('option');
                    dayOption.value = dayOption.innerHTML = k;
                    if (k == defaults.FirstDayValue) {
                        dayOption.setAttribute('selected', 'true');
                    }
                    $DaySelector.append(dayOption);
                }
            }
        }

        $MonthSelector.on('change', function () {
            BuildDay();
        });
        $YearSelector.on('change', function () {
            BuildDay();
        });

        return {
            BuildDay: BuildDay
        }
    }

    if (s('.ipar-ui-i-profile').length > 0 || s('.ipar-ui-i-profile-post').length > 0)
        z.ready(function () {
            init();
        });
});
