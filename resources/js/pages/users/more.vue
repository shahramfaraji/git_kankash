<template>
  <div class="row">
    <div v-if="user.role == 1" class="col-lg-12 m-auto">
      <card v-if="mustVerifyEmail" :title="$t('register')">
        <div class="alert alert-success" role="alert">
          {{ $t("verify_email_address") }}
        </div>
      </card>
      <card v-else :title="$t('ثبت سرویس برای ' + this.$route.params.name)">
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
    <div v-if="user.role == 1" class="col-lg-12 m-auto mt-3">
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
              <v-button :loading="form_filter.busy">
                {{ $t("ثبت") }}
              </v-button>

              <!-- GitHub Register Button -->
              <login-with-github />
            </div>
          </div>
        </form>
      </card>
    </div>
    <div class="col-lg-12 mt-3 mb-5">
      <card :title="$t('سرویس های ' + this.$route.params.name)">
        <div class="row">
          <div class="col-md-11">
            <small>
              شما در اینجا به صورت پیش فرض 100 سرویس آخر
              {{ this.$route.params.name }} را مشاهده میکنید اما با فلتر کردن می
              توانید هر تعداد در میان تاریخ ها را مشاهده کنید.
            </small>
          </div>
          <div class="col-md-1">
            <button
              type="button"
              class="btn btn-outline-primary"
              @click="print"
            >
              <font-awesome-icon :icon="icons.faCog" :fixed-width="true" />
            </button>
          </div>
        </div>
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col">تاریخ</th>
              <th scope="col">همراهان</th>
              <th scope="col">شهر</th>
              <th scope="col">سازمان</th>
              <th scope="col">هزینه</th>
              <th scope="col">هزینه ماموریت</th>
              <th scope="col">برگه ماموریت</th>
              <th scope="col">بیشتر</th>
              <th scope="col" v-if="user.role == 1">ویرایش</th>
              <th scope="col" v-if="user.role == 1">حذف</th>
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
              <td :class="[item.pay == null ? 'text-danger' : 'text-success']">
                {{ item.price }}
              </td>
              <td>
                {{ item.mission_price }}
              </td>
              <td v-if="user.role == 1" class="text-center">
                <router-link
                  :to="{
                    name: 'mission_edit',
                    params: {
                      id: item.id,
                      userid: $route.params.id,
                      name: $route.params.name,
                    },
                  }"
                  class="dropdown-item ps-3 text-center"
                >
                  <font-awesome-icon
                    :icon="icons.faBullseye"
                    :fixed-width="true"
                    class="text-primary pointer-cursor"
                  />
                </router-link>
              </td>
              <td
                :class="[item.price != null ? 'text-success' : 'text-success']"
                class="pointer-cursor"
              >
                <font-awesome-icon
                  :icon="icons.faInfoCircle"
                  :fixed-width="true"
                  @click="openModal(item)"
                />
              </td>
              <td v-if="user.role == 1">
                <router-link
                  :to="{
                    name: 'service_edit',
                    params: {
                      id: item.id,
                      userid: $route.params.id,
                      name: $route.params.name,
                    },
                  }"
                  class="dropdown-item ps-3 t_r"
                >
                  <font-awesome-icon
                    :icon="icons.faEdit"
                    :fixed-width="true"
                    class="text-warning pointer-cursor"
                  />
                </router-link>
              </td>
              <td v-if="user.role == 1">
                <font-awesome-icon
                  :icon="icons.faTrash"
                  :fixed-width="true"
                  class="text-danger pointer-cursor"
                  @click="openModal_delete(item)"
                />
              </td>
            </tr>
            <tr
              v-if="data.service == 0 || data.service == undefined"
              class="iran"
            >
              <td colspan="9" class="text-center">موردی یافت نشد</td>
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
                    <td>
                      <span v-if="selectedRow.price != null">
                        {{ selectedRow.price }}
                      </span>
                      <span v-else>
                        <form
                          v-if="user.role == 1"
                          @submit.prevent="conf_price"
                          @keydown="form_price.onKeydown($event)"
                        >
                          <div class="row">
                            <div class="col-md-9">
                              <input
                                v-model="form_price.price"
                                :class="{
                                  'is-invalid': form_price.errors.has('price'),
                                }"
                                class="form-control"
                                type="number"
                                name="price"
                              />
                              <has-error :form="form_price" field="price" />
                            </div>
                            <div class="col-md-3">
                              <!-- Submit Button -->
                              <v-button :loading="form_price.busy">
                                {{ $t("تایید") }}
                              </v-button>

                              <!-- GitHub Register Button -->
                              <login-with-github />
                            </div>
                          </div>
                        </form>
                      </span>
                    </td>
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
    <!-- Modal -->
    <transition name="fade">
      <div
        v-if="modalVisible_delete"
        class="modal fade show"
        style="display: block"
        tabindex="-1"
        role="dialog"
        @click="closeModalOutside_delete"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                آیا از حذف این سرویس {{ selectedRow_delete.user.user }} مطمعن
                هستید؟
              </h1>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="closeModal_delete"
              >
                خیر
              </button>
              <button
                type="button"
                class="btn btn-danger"
                @click="deleteservice(selectedRow_delete.id)"
              >
                بله ادامه بده
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Form from "vform";
import LoginWithGithub from "~/components/LoginWithGithub";
import axios from "axios";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
  faEdit,
  faTrash,
  faInfoCircle,
  faPrint,
  faCog,
  faBullseye
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
      faCog,
      faBullseye
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
      alongs: null,
    }),
    form_price: new Form({
      price: null,
    }),
    data: [],
    mustVerifyEmail: false,
    modalVisible: false,
    selectedRow: null,
    modalVisible_delete: false,
    selectedRow_delete: null,
  }),
  computed: mapGetters({
    user: "auth/user",
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
      this.form_price.price = item.city.price;
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
    openModal_delete(item) {
      this.selectedRow_delete = item;
      this.modalVisible_delete = true;
    },
    closeModal_delete() {
      this.modalVisible_delete = false;
    },
    closeModalOutside_delete(event) {
      // بستن مودال اگر کلیک بروی پس‌زمینه مودال اتفاق بیفتد
      if (event.target === event.currentTarget) {
        this.closeModal_delete();
      }
    },
    convertToJalali(date) {
      const jalaliMoment = moment(date, "YYYY-MM-DD").locale("fa");
      let jalaliDate = jalaliMoment.format("YYYY-MM-DD");
      return jalaliDate;
    },
    print() {
      this.$router.push({
        name: "users_more_print",
        params: {
          id: this.$route.params.id,
          name: this.$route.params.name,
        },
      });
    },
    async filter() {
      this.form_filter.id = this.$route.params.id;
      await this.form_filter
        .post("/api/filter_services_admin")
        .then((response) => {
          this.data = response.data;
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    async fetchData() {
      await axios
        .get("/api/services_admin/" + this.$route.params.id)
        .then((response) => {
          this.data = response.data;
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    async conf_price() {
      const id = this.selectedRow.id;
      const { data } = await this.form_price.post(
        "/api/edit_service_price_admin/" + id
      );
      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchData();
        this.closeModal();
      }
    },
    async create() {
      // Register the user.
      this.form.id = this.$route.params.id;
      const { data } = await this.form.post("/api/create_service_admin");
      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchData();
      }
    },
    async deleteservice(id) {
      const { data } = await this.form.post("/api/delete_service/" + id);
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchData();
        this.closeModal_delete();
      }
    },
  },
};
</script>
