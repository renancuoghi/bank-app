<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar class="bg-primary text-white">
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title> BNB Bank </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-drawer
      class="bg-primary text-white"
      v-model="leftDrawerOpen"
      show-if-above
      bordered
    >
      <q-list>
        <q-item-label class="text-white" header> BNB Bank </q-item-label>

        <EssentialLink
          v-for="link in essentialLinks"
          :key="link.title"
          v-bind="link"
        />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import EssentialLink from 'components/EssentialLink.vue';
import { userStore } from 'src/stores/auth';

const linksList = [
  {
    title: 'BALANCE',
    caption: 'Balance',
    icon: 'balance',
    link: '/',
    permission: 'user',
  },
  {
    title: 'INCOMES',
    caption: 'Incomes',
    icon: 'arrow_upward',
    link: '#/checks',
    permission: 'user',
  },
  {
    title: 'EXPENSES',
    caption: 'Incomes',
    icon: 'arrow_downward',
    link: '#/expenses',
    permission: 'user',
  },
  {
    title: 'PENDING',
    caption: 'Pending',
    icon: 'publish',
    link: '#/admin/home',
    permission: 'admin',
  },
  {
    title: 'Logout',
    caption: 'logout',
    icon: 'person_off',
    link: '#',
    permission: 'all',
  },
];

export default defineComponent({
  name: 'MainLayout',

  components: {
    EssentialLink,
  },

  setup() {
    const leftDrawerOpen = ref(false);
    const is_admin = userStore().getUser?.data?.user.is_admin;

    return {
      essentialLinks: linksList.filter((f) => {
        if (is_admin) {
          return f.permission === 'all' || f.permission === 'admin';
        }
        return f.permission === 'all' || f.permission === 'user';
      }),
      leftDrawerOpen,
      toggleLeftDrawer() {
        leftDrawerOpen.value = !leftDrawerOpen.value;
      },
    };
  },
});
</script>
