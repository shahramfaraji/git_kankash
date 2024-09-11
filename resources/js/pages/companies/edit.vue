<template>
  <div class="row">
    <div class="col-lg-5 m-auto fixed">
      <card :title="$t('ویرایش سازمان')">
        <form @submit.prevent="create" @keydown="form.onKeydown($event)">
          <!-- Name -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("نام سازمان")
            }}</label>
            <div class="col-md-7">
              <input
                v-model="form.name"
                :class="{ 'is-invalid': form.errors.has('name') }"
                class="form-control"
                type="text"
                name="name"
              />
              <has-error :form="form" field="name" />
            </div>
          </div>

          <div class="mb-3 row">
            <div class="col-md-7 offset-md-3 d-flex">
              <!-- Submit Button -->
              <v-button :loading="form.busy">
                {{ $t("ثبت") }}
              </v-button>

              <!-- GitHub Register Button -->
              <login-with-github />
            </div>
          </div>
        </form>
      </card>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import LoginWithGithub from "~/components/LoginWithGithub";
import axios from "axios";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
  faEdit,
  faTrash,
  faInfoCircle,
} from "@fortawesome/free-solid-svg-icons";

export default {
  components: {
    LoginWithGithub,
    FontAwesomeIcon,
  },

  middleware: "admin",

  metaInfo() {
    return { title: this.$t("سازمانها") };
  },

  data: () => ({
    icons: {
      faEdit,
      faTrash,
      faInfoCircle,
    },
    form: new Form({
      name: "",
    }),
    companies: [],
    mustVerifyEmail: false,
    modalVisible: false,
    selectedRow: null,
  }),
  mounted() {
    this.fetchCities();
  },
  methods: {
    openModal(item) {
      this.selectedRow = item;
      this.modalVisible = true;
    },
    closeModal() {
      this.modalVisible = false;
    },
    closeModalOutside(event) {
      // بستن مودال اگر کلیک بروی پس‌زمینه مودال اتفاق بیفتد
      if (event.target === event.currentTarget) {
        this.closeModal();
      }
    },
    async fetchCities() {
      await axios
        .get("/api/company/" + this.$route.params.id)
        .then((response) => {
          this.form.name = response.data.com.name;
        })
        .catch((error) => {
          console.error("Error fetching city", error);
        });
    },
    async create() {
      // Register the user.
      const { data } = await this.form.post(
        "/api/company_update/" + this.$route.params.id
      );

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        // Log in the user.
        this.$router.push({ name: "companies" });
      }
    },
  },
};
</script>
