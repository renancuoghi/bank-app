<template>
  <q-item clickable tag="a" :href="link" @click="checkLogout(caption)">
    <q-item-section v-if="icon" avatar>
      <q-icon :name="icon" />
    </q-item-section>

    <q-item-section>
      <q-item-label>{{ title }}</q-item-label>
    </q-item-section>
  </q-item>
</template>

<script lang="ts">
import UserApi from 'src/api/user/userapi';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'EssentialLink',
  props: {
    title: {
      type: String,
      required: true,
    },

    caption: {
      type: String,
      default: '',
    },

    link: {
      type: String,
      default: '#',
    },

    icon: {
      type: String,
      default: '',
    },
  },

  setup() {
    const userApi = new UserApi();
    function checkLogout(caption: string) {
      if (caption == 'logout') {
        userApi.logout();
      }
    }

    return {
      checkLogout,
    };
  },
});
</script>
