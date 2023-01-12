<template>
  <div class="q-pa-sm">
    <q-form class="q-gutter-md">
      <q-input rounded outlined v-model="state.username" label="username" />

      <q-input
        rounded
        outlined
        v-model="state.password"
        type="password"
        label="password"
      />
      <q-space />
      <div>
        <q-btn
          label="Sign in"
          class="login-button"
          type="button"
          color="primary"
          :size="'lg'"
          @click="onLoginClick"
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
          label="Create an account"
          @click="goToRegister"
        />
      </div>
    </q-form>
  </div>
</template>

<script lang="ts">
import { useQuasar } from 'quasar';
import UserApi from 'src/api/user/userapi';
import { defineComponent, ref } from 'vue';
import router from 'src/router';

export default defineComponent({
  name: 'LoginPage',

  setup() {
    const $q = useQuasar();
    const userApi = new UserApi();

    const state = ref({
      username: '',
      password: '',
    });

    function onLoginClick() {
      userApi.login(state.value, (error: string) => {
        $q.notify({
          message: error,
          color: 'danger',
        });
      });
    }

    function goToRegister() {
      router.push({ name: 'register' });
    }

    return {
      state,
      onLoginClick,
      goToRegister,
    };
  },
});
</script>
<style lang="scss" scoped>
.login-button {
  width: 100%;
}
</style>
