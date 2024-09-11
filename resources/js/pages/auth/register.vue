<template>
  <div class="row">
    <div class="col-lg-7 m-auto">
      <card v-if="mustVerifyEmail" :title="$t('register')">
        <div class="alert alert-success" role="alert">
          {{ $t("verify_email_address") }}
        </div>
      </card>
      <card v-else :title="$t('ثبت نام')">
        <form @submit.prevent="register" @keydown="form.onKeydown($event)">
          <!-- admin -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">
              {{ $t("مدیر") }}
            </label>
            <div class="col-md-7">
              <div class="form-check form-check d-flex justify-content-start">
                <input
                  v-model="form.role"
                  class="form-check-input float-right mt-3"
                  type="checkbox"
                  id="adminCheckbox"
                  name="role"
                />
              </div>
              <has-error :form="form" field="role" />
            </div>
          </div>

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
                {{ $t("ثبت نام") }}
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

export default {
  components: {
    LoginWithGithub,
  },

  middleware: "admin",

  metaInfo() {
    return { title: this.$t("ثبت نام") };
  },

  data: () => ({
    form: new Form({
      role: "",
      user: "",
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
    }),
    mustVerifyEmail: false,
  }),

  methods: {
    async register() {
      // Register the user.
      const { data } = await this.form.post("/api/register");

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        // Log in the user.
        const {
          data: { token },
        } = await this.form.post("/api/login");

        // Save the token.
        this.$store.dispatch("auth/saveToken", { token });

        // Update the user.
        await this.$store.dispatch("auth/updateUser", { user: data });

        // Redirect home.
        this.$router.push({ name: "home" });
      }
    },
  },
};
</script>
