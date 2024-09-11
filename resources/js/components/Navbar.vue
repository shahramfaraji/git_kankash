<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
      <router-link
        :to="{ name: user ? 'home' : 'home' }"
        class="navbar-brand iran"
      >
        {{ appName }}
      </router-link>
      <ul class="navbar-nav ms-auto">
          <!-- Authenticated -->
          <li v-if="user.role == 1 || user.role == 2" class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle text-dark"
              href="#"
              role="button"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
            {{ $t("منو") }}
            </a>
            <div class="dropdown-menu">
              <router-link
                v-if="user.role == 1"
                :to="{ name: 'dashboard' }"
                class="dropdown-item ps-3 t_r"
              > 
              <font-awesome-icon
                    :icon="icons.faChartBar"
                    :fixed-width="true"
                  />
                {{ $t("داشبورد") }}
              </router-link>

              <div v-if="user.role == 1" class="dropdown-divider" />

              <router-link
                :to="{ name: 'users' }"
                class="dropdown-item ps-3 t_r"
              >
                <fa icon="user" fixed-width />
                {{ $t("کاربران") }}
              </router-link>

              <div v-if="user.role == 1" class="dropdown-divider" />

              <router-link
                v-if="user.role == 1"
                :to="{ name: 'cities' }"
                class="dropdown-item ps-3 t_r"
              >
              <font-awesome-icon
                    :icon="icons.faCity"
                    :fixed-width="true"
                  />
                {{ $t("شهرها") }}
              </router-link>

              <div v-if="user.role == 1" class="dropdown-divider" />

              <router-link
                v-if="user.role == 1"
                :to="{ name: 'companies' }"
                class="dropdown-item ps-3 t_r"
              >
              <font-awesome-icon
                    :icon="icons.faBuilding"
                    :fixed-width="true"
                  />
                {{ $t("سازمان ها") }}
              </router-link>

            </div>
          </li>
      </ul>

      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbar"
      >
        <span class="navbar-toggler-icon" />
      </button>

      <!-- <div id="navbar" class="collapse navbar-collapse">
        <ul class="navbar-nav">
          <locale-dropdown /> -->
          <!-- <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li> -->
        <!-- </ul> -->

        <ul class="navbar-nav me-auto">
          <!-- Authenticated -->
          <li v-if="user" class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle text-dark"
              href="#"
              role="button"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <img
                src="/profile.png"
                class="rounded-circle profile-photo me-1"
              />
              {{ user.user }}
            </a>
            <div class="dropdown-menu">
              <router-link
                :to="{ name: 'settings.profile' }"
                class="dropdown-item ps-3 t_r"
              >
                <fa icon="cog" fixed-width />
                {{ $t("تنظیمات") }}
              </router-link>

              <div class="dropdown-divider" />
              <a href="#" class="dropdown-item ps-3 t_r" @click.prevent="logout">
                <fa icon="sign-out-alt" fixed-width />
                {{ $t("خروج") }}
              </a>
            </div>
          </li>
          <!-- Guest -->
          <template v-else>
            <li class="nav-item">
              <router-link
                :to="{ name: 'login' }"
                class="nav-link"
                active-class="active"
              >
                {{ $t("ورود") }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link
                :to="{ name: 'register' }"
                class="nav-link"
                active-class="active"
              >
                {{ $t("ثبت نام") }}
              </router-link>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { mapGetters } from "vuex";
import LocaleDropdown from "./LocaleDropdown";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
  faCity,
  faBuilding,
  faChartBar 
} from "@fortawesome/free-solid-svg-icons";

export default {
  components: {
    LocaleDropdown,
    FontAwesomeIcon,
  },

  data: () => ({
    appName: window.config.appName,
    icons: {
      faCity,
      faBuilding,
      faChartBar 
    },
  }),

  computed: mapGetters({
    user: "auth/user",
  }),

  methods: {
    async logout() {
      // Log out the user.
      await this.$store.dispatch("auth/logout");

      // Redirect to login.
      this.$router.push({ name: "login" });
    },
  },
};
</script>

<style scoped>
.profile-photo {
  width: 2rem;
  height: 2rem;
  margin: -0.375rem 0;
}

.container {
  max-width: 1100px;
}
</style>
