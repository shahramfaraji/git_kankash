<template>
  <div class="row">
    <div v-if="user.role == 1" class="col-lg-12 m-auto">
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
                {{ $t("ثبت نام") }}
              </v-button>

              <!-- GitHub Register Button -->
              <login-with-github />
            </div>
          </div>
        </form>
      </card>
    </div>
    <div class="col-lg-12 mt-3 mb-5">
      <card :title="$t('لیست کاربران')">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">سمت</th>
              <th scope="col">نام و نام خانوادگی</th>
              <th scope="col">نام کاربری</th>
              <th scope="col">ایمیل</th>
              <th scope="col">بیشتر</th>
              <th scope="col" v-if="user.role == 1">ویرایش</th>
              <th scope="col" v-if="user.role == 1">حذف</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="users.length == 0" class="iran">
              <td colspan="8" class="text-center">موردی یافت نشد</td>
            </tr>
            <tr v-for="(_user, index) in users" :key="user.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>
                <span v-if="_user.role != 0" class="text-danger">
                  <span v-if="_user.role == 1">مدیر سطح1</span>
                  <span v-if="_user.role == 2">مدیر سطح2</span>
                </span>
                <span v-else>کارشناس</span>
              </td>
              <td>{{ _user.user }}</td>
              <td>{{ _user.name }}</td>
              <td>{{ _user.email }}</td>
              <td>
                <router-link
                  :to="{
                    name: 'users_more',
                    params: { id: _user.id, name: _user.user },
                  }"
                  class="dropdown-item ps-3 t_r"
                >
                  <font-awesome-icon
                    :icon="icons.faInfoCircle"
                    :fixed-width="true"
                    class="text-primary"
                  />
                </router-link>
              </td>
              <td v-if="user.role == 1">
                <router-link
                  :to="{ name: 'users_edit', params: { id: _user.id } }"
                  class="dropdown-item ps-3 t_r"
                >
                  <font-awesome-icon
                    :icon="icons.faEdit"
                    :fixed-width="true"
                    class="text-warning"
                  />
                </router-link>
              </td>
              <td v-if="user.role == 1">
                <font-awesome-icon
                  v-if="count_admin > 1 || _user.role == 0"
                  :icon="icons.faTrash"
                  :fixed-width="true"
                  class="text-danger pointer-cursor"
                  @click="openModal(_user)"
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
                آیا از حذف {{ selectedRow.user }} مطمعن هستید؟
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
import { mapGetters } from "vuex";
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
    users: [],
    count_admin: 0,
    mustVerifyEmail: false,
    modalVisible: false,
    selectedRow: null,
  }),
  computed: mapGetters({
    user: "auth/user",
  }),

  mounted() {
    this.fetchUsers();
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
    async fetchUsers() {
      await axios
        .get("/api/all_users")
        .then((response) => {
          this.users = response.data.users;
          this.count_admin = response.data.count_admin;
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    async register() {
      // Register the user.
      this.form.role = parseInt(this.form.role);
      const { data } = await this.form.post("/api/register");

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchUsers();
      }
    },
    async deleteuser(id) {
      const { data } = await this.form.post("/api/delete_user/" + id);
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchUsers();
        this.closeModal();
      }
    },
  },
};
</script>
