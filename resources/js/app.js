import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;


import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

window.Calendar = Calendar;
window.dayGridPlugin = dayGridPlugin;