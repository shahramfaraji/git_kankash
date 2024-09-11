<template>
  <div class="row">
    <div class="col-lg-12 m-auto">
      <card v-if="mustVerifyEmail" :title="$t('register')">
        <div class="alert alert-success" role="alert">
          {{ $t("verify_email_address") }}
        </div>
      </card>
      <card v-else :title="$t('ثبت سرویس')">
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
    <div class="col-lg-12 m-auto mt-3">
      <card :title="$t('فیلتر')">
        <form @submit.prevent="filter" @keydown="form.onKeydown($event)">
          <!-- Name -->
          <div class="mb-3 row">
            <label class="col-md-2 col-form-label text-md-end">{{
              $t("شروع تاریخ")
            }}</label>
            <div class="col-md-4">
              <date-picker
                v-model="form_filter.start"
                simple
                auto-submit
                :class="{ 'is-invalid': form.errors.has('start') }"
                class="form-control"
              />
              <has-error :form="form_filter" field="start" />
            </div>
            <label class="col-md-2 col-form-label text-md-end">{{
              $t("شروع پایان")
            }}</label>
            <div class="col-md-4">
              <date-picker
                v-model="form_filter.end"
                simple
                auto-submit
                :class="{ 'is-invalid': form.errors.has('end') }"
                class="form-control"
              />
              <has-error :form="form_filter" field="end" />
            </div>
          </div>

          <div class="mb-3 row">
            <div class="col-md-7 offset-md-3 d-flex">
              <!-- Submit Button -->
              <v-button :loading="form.busy">
                {{ $t("فیلتر") }}
              </v-button>

              <!-- GitHub Register Button -->
              <login-with-github />
            </div>
          </div>
        </form>
      </card>
    </div>
    <div class="col-lg-12 mt-3 mb-5">
      <card :title="$t('سرویس های من')">
        <small>
          شما در اینجا به صورت پیش فرض 100 سرویس آخر خود را مشاهده میکنید اما با
          فلتر کردن می توانید هر تعداد در میان تاریخ ها را مشاهده کنید.
        </small>
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col">تاریخ</th>
              <th scope="col">همراهان</th>
              <th scope="col">شهر</th>
              <th scope="col">سازمان</th>
              <th scope="col">هزینه</th>
              <th scope="col">بیشتر</th>
              <!-- <th scope="col">ویرایش</th>
              <th scope="col">حذف</th> -->
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data.service" :key="item.id">
              <td :class="[item.price != null ? 'text-success' : 'text-danger']">
                {{ convertToJalali(item.date) }}
              </td>
              <td>
                {{ item.along.length }}
              </td>
              <td>
                <span v-if="item.city != null">{{ item.city.name }}</span>
              </td>
              <td>
                <span v-if="item.company != null">{{ item.company.name }}</span>
              </td>
              <td :class="[item.pay != null ? 'text-danger' : 'text-success']">
                {{ item.price }}
              </td>
              <td
                :class="[item.price != null ? 'text-success' : 'text-danger']"
                class="pointer-cursor"
              >
                <font-awesome-icon
                  :icon="icons.faInfoCircle"
                  :fixed-width="true"
                  @click="openModal(item)"
                />
              </td>
            </tr>
            <tr
              v-if="data.service == 0 || data.service == undefined"
              class="iran"
            >
              <td colspan="8" class="text-center">موردی یافت نشد</td>
            </tr>
          </tbody>
        </table>
      </card>
    </div>
    <!-- Modal -->
    <transition name="fade">
      <div
        v-if="modalVisible"
        class="modal fade show"
        style="display: block"
        tabindex="-1"
        role="dialog"
        @click="closeModalOutside"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                جزئیات سرویس
              </h1>
            </div>
            <div class="modal-body">
              <table class="table">
                <tbody>
                  <tr>
                    <th>تاریخ</th>
                    <td>{{ convertToJalali(selectedRow.date) }}</td>
                  </tr>
                  <tr>
                    <th>شهر</th>
                    <td>
                      <span v-if="selectedRow.city != null">{{
                        selectedRow.city.name
                      }}</span>
                    </td>
                  </tr>
                  <tr>
                    <th>سازمان</th>
                    <td>
                      <span v-if="selectedRow.company != null">{{
                        selectedRow.company.name
                      }}</span>
                    </td>
                  </tr>
                  <tr>
                    <th>همراهان</th>
                    <td>
                      <ol v-if="selectedRow.along.length > 0">
                        <li v-for="item in selectedRow.along" :key="item.id">
                          {{ along_item(item.along_id) }}
                        </li>
                      </ol>
                      <span v-else> همراه نداشته </span>
                    </td>
                  </tr>
                  <tr>
                    <th>وضعیت</th>
                    <td>
                      <span v-if="selectedRow.pay != 1" class="text-danger">
                        پرداخت نشده
                      </span>
                      <span v-else class="text-success"> پرداخت شده </span>
                    </td>
                  </tr>
                  <tr>
                    <th>هزینه</th>
                    <td>{{ selectedRow.price }}</td>
                  </tr>
                  <tr>
                    <th>توضیحات</th>
                    <td>{{ selectedRow.more }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="closeModal"
              >
                بستن
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
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

  middleware: "auth",

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
      start: null,
      end: null,
    }),
    form: new Form({
      date: null,
      city: null,
      company: null,
      more: null,
      alongs: [],
    }),
    data: [],
    mustVerifyEmail: false,
    modalVisible: false,
    selectedRow: null,
  }),
  mounted() {
    this.fetchData();
  },
  methods: {
    along_item(id) {
      const foundUser = this.data.user.find((item) => item.id === id);
      return foundUser.text;
    },
    openModal(item) {
      this.selectedRow = item;
      this.modalVisible = true;
    },
    closeModal() {
      this.modalVisible = false;
    },
    closeModalOutside(event) {
      if (event.target === event.currentTarget) {
        this.closeModal();
      }
    },
    convertToJalali(date) {
      const jalaliMoment = moment(date, "YYYY-MM-DD").locale("fa");
      let jalaliDate = jalaliMoment.format("YYYY-MM-DD");
      return jalaliDate;
    },
    async filter() {
      await this.form_filter
        .post("/api/filter_services")
        .then((response) => {
          this.data = response.data;
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    async fetchData() {
      await axios
        .get("/api/services")
        .then((response) => {
          this.data = response.data;
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    async create() {
      // Register the user.
      const { data } = await this.form.post("/api/create_service");
      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchData();
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
<style scoped>
.select2-container {
  z-index: 1000000 !important;
}
</style>
