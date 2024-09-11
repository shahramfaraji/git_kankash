<template>
  <div class="row">
    <div class="col-lg-12 m-auto">
      <card v-if="mustVerifyEmail" :title="$t('register')">
        <div class="alert alert-success" role="alert">
          {{ $t("verify_email_address") }}
        </div>
      </card>
      <card v-else :title="$t('ویرایش اطلاعات کاربر')">
        <form @submit.prevent="register" @keydown="form.onKeydown($event)">
          <!-- admin -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">
              {{ $t("مدیر") }}
            </label>
            <div class="col-md-7">
              <div class="form-check form-check justify-content-start" style="display: grid!important;">
                <div class="form-check">                  
                  <input class="form-check-input" v-model="form.role" type="radio" name="role" id="exampleRadios1" value="1">
                  <label class="form-check-label" for="exampleRadios1">
                    سطح 1
                  </label>
                </div>
                <div class="form-check">                  
                  <input class="form-check-input" v-model="form.role" type="radio" name="role" id="exampleRadios2" value="2">  
                  <label class="form-check-label" for="exampleRadios2">
                    سطح 2
                  </label>                
                </div>
              </div>
            </div>
          </div>
          <!-- Name -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("نام و نام خانوادگی")
            }}</label>
            <div class="col-md-7">
              <input
                v-model="form.user"
                :class="{ 'is-invalid': form.errors.has('user') }"
                class="form-control"
                type="text"
                name="user"
              />
              <has-error :form="form" field="user" />
            </div>
          </div>

          <!-- Name -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("نام کاربری")
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

          <!-- Email -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("ایمیل")
            }}</label>
            <div class="col-md-7">
              <input
                v-model="form.email"
                :class="{ 'is-invalid': form.errors.has('email') }"
                class="form-control"
                type="email"
                name="email"
              />
              <has-error :form="form" field="email" />
            </div>
          </div>

          <!-- Password -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("رمز عبور")
            }}</label>
            <div class="col-md-7">
              <input
                v-model="form.password"
                :class="{ 'is-invalid': form.errors.has('password') }"
                class="form-control"
                type="password"
                name="password"
              />
              <has-error :form="form" field="password" />
            </div>
          </div>

          <!-- Password Confirmation -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("تکرار رمز عبور")
            }}</label>
            <div class="col-md-7">
              <input
                v-model="form.password_confirmation"
                :class="{
                  'is-invalid': form.errors.has('password_confirmation'),
                }"
                class="form-control"
                type="password"
                name="password_confirmation"
              />
              <has-error :form="form" field="password_confirmation" />
            </div>
          </div>

          <div class="mb-3 row">
            <div class="col-md-7 offset-md-3 d-flex">
              <!-- Submit Button -->
              <v-button :loading="form.busy">
                {{ $t("ویرایش") }}
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
    return { title: this.$t("ثبت نام") };
  },

  data: () => ({
    icons: {
      faEdit,
      faTrash,
      faInfoCircle,
    },
    form: new Form({
      role: false,
      user: "",
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
    }),
    mustVerifyEmail: false,
  }),
  mounted() {
    this.fetchUser();
  },
  methods: {
    async fetchUser() {
      await axios
        .get("/api/user/" + this.$route.params.id)
        .then((response) => {
          this.form.role = response.data.user.role;
          this.form.user = response.data.user.user;
          this.form.name = response.data.user.name;
          this.form.email = response.data.user.email;
        })
        .catch((error) => {
          console.error("Error fetching user", error);
        });
    },
    async register() {
      // Register the user.
      const { data } = await this.form.post(
        "/api/user_update/" + this.$route.params.id
      );

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        // Log in the user.
        this.$router.push({ name: "users" });
      }
    },
  },
};
</script>
