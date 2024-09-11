<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import frLocale from "@fullcalendar/core/locales/fa";

export default {
  middleware: "auth",
  components: {
    FullCalendar, // make the <FullCalendar> tag available
  },
  data() {
    return {
      calendarOptions: {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: "dayGridMonth",
        dateClick: this.handleDateClick,
        locale: "fa",
        direction: "rtl",
        firstDay: 6,
        isJalaali: true,
        navLinks: true, // can click day/week names to navigate views
        editable: true,
        eventLimit: true,
        events: [
          { title: "event 1", date: "2023-11-24" },
          { title: "event 1", date: "2023-11-24" },
          { title: "event 2", date: "1402-09-02" },
        ],
      },
    };
  },
  methods: {
    handleDateClick: function (arg) {
      alert("date click! " + arg.dateStr);
    },
  },
};
</script>
<template>
  <FullCalendar :options="calendarOptions" />
</template>
