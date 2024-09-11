<template>
  <card :title="$t('رمز عبور شما')">
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
      <alert-success :form="form" :message="$t('password_updated')" />

      <!-- Password -->
      <div class="mb-3 row">
        <label class="col-md-3 col-form-label text-md-end">{{
          $t("رمز عبور جدید")
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
          $t("تکرار رمز عبور جدید")
        }}</label>
        <div class="col-md-7">
          <input
            v-model="form.password_confirmation"
            :class="{ 'is-invalid': form.errors.has('password_confirmation') }"
            class="form-control"
            type="password"
            name="password_confirmation"
          />
          <has-error :form="form" field="password_confirmation" />
        </div>
      </div>

      <!-- Submit Button -->
      <div class="mb-3 row">
        <div class="col-md-9 ms-md-auto">
          <v-button :loading="form.busy" type="success">
            {{ $t("ویرایش") }}
          </v-button>
        </div>
      </div>
    </form>
  </card>
</template>

<script>
import Form from "vform";

export default {
  scrollToTop: false,

  metaInfo() {
    return { title: this.$t("تنظیمات") };
  },

  data: () => ({
    form: new Form({
      password: "",
      password_confirmation: "",
    }),
  }),

  methods: {
    async update() {
      await this.form.patch("/api/settings/password");

      this.form.reset();
    },
  },
};
</script>
