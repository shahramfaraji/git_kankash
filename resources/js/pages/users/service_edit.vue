<template>
  <div class="row">
    <div class="col-lg-12 m-auto">
      <card :title="$t('ویرایش سرویس ' + this.$route.params.name)">
        <form @submit.prevent="create" @keydown="form.onKeydown($event)">
          <!-- Name -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("تاریخ *")
            }}</label>
            <div class="col-md-7">
              <date-picker
                v-model="form.date"
                simple
                auto-submit
                :class="{ 'is-invalid': form.errors.has('date') }"
                class="form-control"
              />
              <has-error :form="form" field="date" />
            </div>
          </div>

          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("شهر*")
            }}</label>
            <div class="col-md-7">
              <select
                v-model="form.city"
                :class="{ 'is-invalid': form.errors.has('city') }"
                class="form-control iran"
              >
                <option
                  v-for="item in data.city"
                  :key="item.id"
                  :value="item.id"
                  calss="iran"
                >
                  <span class="iran">{{ item.name }}</span>
                </option>
              </select>
              <has-error :form="form" field="city" />
            </div>
          </div>

          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("سازمان")
            }}</label>
            <div class="col-md-7">
              <select v-model="form.company" class="form-control">
                <option calss="iran" :value="null">
                  <span class="iran"></span>
                </option>
                <option
                  v-for="item in data.company"
                  :key="item.id"
                  calss="iran"
                  :value="item.id"
                >
                  <span class="iran">{{ item.name }}</span>
                </option>
              </select>
              <has-error :form="form" field="company" />
            </div>
          </div>

          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("همراهان")
            }}</label>
            <div class="col-md-7">
              <Select2
                v-model="form.alongs"
                :options="data.user"
                :settings="{ multiple: true }"
                class="select2_custom"
              />
              <has-error :form="form" field="alongs" />
            </div>
          </div>

          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("هزینه")
            }}</label>
            <div class="col-md-7">
              <input
                v-model="form.price"
                :class="{ 'is-invalid': form.errors.has('price') }"
                class="form-control"
                type="number"
                name="price"
              />
              <has-error :form="form" field="price" />
            </div>
          </div>

          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("توضیحات")
            }}</label>
            <div class="col-md-7">
              <textarea
                v-model="form.more"
                class="form-control"
                id="exampleFormControlTextarea1"
                rows="3"
              ></textarea>
              <has-error :form="form" field="more" />
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
  faPrint,
} from "@fortawesome/free-solid-svg-icons";
import moment from "jalali-moment";
import Select2 from "v-select2-component";

export default {
  components: {
    LoginWithGithub,
    FontAwesomeIcon,
    Select2,
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
      faPrint,
    },
    form_filter: new Form({
      id: null,
      start: null,
      end: null,
    }),
    form: new Form({
      id: null,
      date: null,
      city: null,
      company: null,
      more: null,
      price: null,
      alongs: [],
    }),
    data: [],
    mustVerifyEmail: false,
    modalVisible: false,
    selectedRow: null,
    x: [],
  }),
  mounted() {
    this.fetchData();
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
    convertToJalali(date) {
      const jalaliMoment = moment(date, "YYYY-MM-DD").locale("fa");
      let jalaliDate = jalaliMoment.format("YYYY-MM-DD");
      return jalaliDate;
    },
    async fetchData() {
      await axios
        .get("/api/service_edit_admin/" + this.$route.params.id)
        .then((response) => {
          this.data = response.data;
          this.form.date = this.convertToJalali(response.data.service.date);
          this.form.city = response.data.service.city_id;
          this.form.company = response.data.service.company_id;
          this.form.more = response.data.service.more;
          this.form.price = response.data.service.price;
          for (let i = 0; i < response.data.service.along.length; i++) {
            this.form.alongs.push(
              response.data.service.along[i].along_id.toString()
            );
          }
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    async create() {
      // Register the user.
      const { data } = await this.form.post(
        "/api/edit_service_admin/" + this.$route.params.id
      );
      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.$router.push({
          name: "users_more",
          params: {
            id: this.$route.params.userid,
            name: this.$route.params.name,
          },
        });
      }
    },
    async deleteuser(id) {
      const { data } = await this.form.post("/api/delete_user/" + id);
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchData();
        this.closeModal();
      }
    },
  },
};
</script>
