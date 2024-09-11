<template>
  <div class="main-layout" dir="rtl">
    <navbar />

    <div class="container-lg mt-4">
      <child />
    </div>
  </div>
</template>

<script>
import Navbar from "~/components/Navbar";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
export default {
  name: "MainLayout",

  components: {
    Navbar,
    FontAwesomeIcon,
  },
};
</script>
