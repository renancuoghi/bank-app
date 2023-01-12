<template>
  <div class="q-pa-md">
    <q-form class="q-gutter-md">
      <q-input rounded outlined v-model="state.username" label="username" />

      <q-input
        rounded
        outlined
        v-model="state.email"
        type="email"
        label="email"
      />

      <q-input
        rounded
        outlined
        v-model="state.password"
        type="password"
        label="password"
      />

      <div>
        <q-btn
          label="Sign up"
          class="register-button"
          type="button"
          color="primary"
          :size="'lg'"
          @click="onRegisterClick"
        />
      </div>

      <div align="center">
        <div style="width: 20%">
          <q-separator color="blue-grey-2" inset />
        </div>
      </div>
      <div align="center">
        <q-btn
          flat
          color="primary"
          label="Already have an account?"
          @click="goToLogin"
        />
      </div>
    </q-form>
  </div>
</template>

<script lang="ts">
import { useQuasar } from 'quasar';
import UserApi from 'src/api/user/userapi';
import { IRegisterResponse } from 'src/models/user/userinterface';
import { defineComponent, ref } from 'vue';
import router from 'src/router';

export default defineComponent({
  name: 'RegisterPage',

  setup() {
    const $q = useQuasar();

    const state = ref({
      username: '',
      password: '',
      email: '',
    });

    function onRegisterClick() {
      const userApi = new UserApi();
      userApi.register(
        state.value,
        (register: IRegisterResponse) => {
          $q.notify({
            message: register.message,
            color: 'primary',
          });
        },

        (error: string) => {
          $q.notify({
            message: error,
            color: 'negative',
          });
        }
      );
    }

    function goToLogin() {
      router.push({ name: 'login' });
    }

    return {
      state,
      onRegisterClick,
      goToLogin,
    };
  },
});
</script>
<style lang="scss" scoped>
.register-button {
  width: 100%;
}
</style>
