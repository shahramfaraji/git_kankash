<template>
  <div class="row">
    <div class="col-lg-4 fixed">
      <card :title="$t('ثبت شهر')">
        <form @submit.prevent="create" @keydown="form.onKeydown($event)">
          <!-- Name -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("نام شهر")
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

          <!-- Name -->
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

          <!-- KM -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("مسافت")
            }}</label>
            <div class="col-md-7">
              <input
                v-model="form.KM"
                :class="{ 'is-invalid': form.errors.has('KM') }"
                class="form-control"
                type="number"
                name="KM"
              />
              <has-error :form="form" field="KM" />
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
    <div class="col-lg-8">
      <card :title="$t('لیست شهرها')">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">نام شهر</th>
              <th scope="col">هزینه</th>
              <th scope="col">مسافت</th>
              <th scope="col">ویرایش</th>
              <th scope="col">حذف</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="cities.length == 0" class="iran">
              <td colspan="6" class="text-center">موردی یافت نشد</td>
            </tr>
            <tr v-for="(city, index) in cities" :key="city.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ city.name }}</td>
              <td>{{ city.price }}</td>
              <td>{{ city.KM }}</td>
              <td>
                <router-link
                  :to="{ name: 'city_edit', params: { id: city.id } }"
                  class="dropdown-item ps-3 t_r"
                >
                  <font-awesome-icon
                    :icon="icons.faEdit"
                    :fixed-width="true"
                    class="text-warning"
                  />
                </router-link>
              </td>
              <td>
                <font-awesome-icon
                  :icon="icons.faTrash"
                  :fixed-width="true"
                  class="text-danger pointer-cursor"
                  @click="openModal(city)"
                />
              </td>
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
                آیا از حذف {{ selectedRow.name }} مطمعن هستید؟
              </h1>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="closeModal"
              >
                خیر
              </button>
              <button
                type="button"
                class="btn btn-danger"
                @click="deleteuser(selectedRow.id)"
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
    return { title: this.$t("شهرها") };
  },

  data: () => ({
    icons: {
      faEdit,
      faTrash,
      faInfoCircle,
    },
    form: new Form({
      name: "",
      price: "",
      KM: ""
    }),
    cities: [],
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
        .get("/api/all_city")
        .then((response) => {
          this.cities = response.data.city;
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    async create() {
      // Register the user.
      const { data } = await this.form.post("/api/create_city");
      console.log(data)
      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchCities();
      }
    },
    async deleteuser(id) {
      const { data } = await this.form.post("/api/delete_city/" + id);
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchCities();
        this.closeModal();
      }
    },
  },
};
</script>
