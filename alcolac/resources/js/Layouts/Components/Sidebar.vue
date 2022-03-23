<template>
  <div>
    <div
      id="admin-sidebar"
      class="
        border-r border-gray-300
        transform
        top-0
        left-0
        w-64
        fixed
        h-full
        overflow-auto
        ease-in-out
        transition-all
        duration-300
        z-30
        sidebar sidebar-fixed
      "
      :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <span id="logo-wrapper" class="flex w-full items-center p-4 border-b">
        <img
          :src="url + '/storage/' + logo['16'].value"
          alt="Logo"
          class="h-24 mx-auto"
        />
      </span>

      <!-- <span
        class="flex w-full items-center border-b"
        v-for="(item, index) in sidebarMenuItems"
      >
        <a
          :href="'/admin/' + item.link"
          :class="
            routess == '/admin/' + item.link || routess == '/admin' + item.link
              ? 'active w-full p-3 text-white other'
              : 'w-full p-3 text-white other'
          "
          >{{ item.name }}</a
        >
      </span> -->
      <ul class="sidebar-nav">
        <template v-for="(item, index) in sidebarMenuItems">
          <li class="nav-item" v-if="item.access_level == 1" :key="index">
            <a
              :href="'/admin/' + item.link"
              :class="
                routess == '/admin/' + item.link ||
                routess == '/admin' + item.link
                  ? 'active nav-link'
                  : 'nav-link'
              "
              v-html="
                (item.link ? icons[item.link] : icons.dashboard) + item.name
              "
            >
            </a>
          </li>
          <li class="nav-group" v-else :key="index" v-on:click="toggleMenu">
            <a
              class="nav-link nav-group-toggle"
              v-html="
                (item.link ? icons[item.link] : icons.dashboard) + item.name
              "
            >
            </a>
            <ul class="nav-group-items" style="height: auto">
              <li
                class="nav-item"
                v-for="(child, childIndex) in sidebarChildMenuItems[index]"
              >
                <a
                  :href="'/admin/' + child.link"
                  :class="
                    routess == '/admin/' + child.link ||
                    routess == '/admin' + child.link
                      ? 'active nav-link'
                      : 'nav-link'
                  "
                  v-html="
                    (child.link ? icons[child.link] : icons.dashboard) +
                    child.name
                  "
                >
                </a>
              </li>
            </ul>
          </li>
        </template>
      </ul>

      <span class="bottom-text">V2.1.0</span>
    </div>
  </div>
</template>

<script>
import JetNavLink from "Jetstream/NavLink";
export default {
  components: {
    // JetNavLink
  },
  data() {
    return {
      sidebarMenuItems: this.$page.data.menuItems.sidebar,
      sidebarChildMenuItems: this.$page.data.menuItems.sidebar_children,
      logo: this.$page.data.logo,
      url: window.location.origin,
      routess: window.location.pathname,
      icons: {
        dashboard:
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<path fill="var(--ci-primary-color, currentColor)" d="M425.706,142.294A240,240,0,0,0,16,312v88H160V368H48V312c0-114.691,93.309-208,208-208s208,93.309,208,208v56H352v32H496V312A238.432,238.432,0,0,0,425.706,142.294Z" class="ci-primary"></path><rect width="32" height="32" x="80" y="264" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="240" y="128" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="136" y="168" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="400" y="264" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><path fill="var(--ci-primary-color, currentColor)" d="M297.222,335.1l69.2-144.173-28.85-13.848L268.389,321.214A64.141,64.141,0,1,0,297.222,335.1ZM256,416a32,32,0,1,1,32-32A32.036,32.036,0,0,1,256,416Z" class="ci-primary"></path></svg>',
        files:
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<path fill="var(--ci-primary-color, currentColor)" d="M334.627,16H48V496H472V153.373ZM440,166.627V168H320V48h1.373ZM80,464V48H288V200H440V464Z" class="ci-primary"></path></svg>',
        "admin-users":
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<path fill="var(--ci-primary-color, currentColor)" d="M411.6,343.656l-72.823-47.334,27.455-50.334A80.23,80.23,0,0,0,376,207.681V128a112,112,0,0,0-224,0v79.681a80.236,80.236,0,0,0,9.768,38.308l27.455,50.333L116.4,343.656A79.725,79.725,0,0,0,80,410.732V496H448V410.732A79.727,79.727,0,0,0,411.6,343.656ZM416,464H112V410.732a47.836,47.836,0,0,1,21.841-40.246l97.66-63.479-41.64-76.341A48.146,48.146,0,0,1,184,207.681V128a80,80,0,0,1,160,0v79.681a48.146,48.146,0,0,1-5.861,22.985L296.5,307.007l97.662,63.479h0A47.836,47.836,0,0,1,416,410.732Z" class="ci-primary"></path></svg>',
        employees:
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<path fill="var(--ci-primary-color, currentColor)" d="M462.541,316.3l-64.344-42.1,24.774-45.418A79.124,79.124,0,0,0,432.093,192V120A103.941,103.941,0,0,0,257.484,43.523L279.232,67a71.989,71.989,0,0,1,120.861,53v72a46.809,46.809,0,0,1-5.215,21.452L355.962,284.8l89.058,58.274a42.16,42.16,0,0,1,19.073,35.421V432h-72v32h104V378.494A74.061,74.061,0,0,0,462.541,316.3Z" class="ci-primary"></path><path fill="var(--ci-primary-color, currentColor)" d="M318.541,348.3l-64.343-42.1,24.773-45.418A79.124,79.124,0,0,0,288.093,224V152A104.212,104.212,0,0,0,184.04,47.866C126.723,47.866,80.093,94.581,80.093,152v72a78,78,0,0,0,9.015,36.775l24.908,45.664L50.047,348.3A74.022,74.022,0,0,0,16.5,410.4L16,496H352.093V410.494A74.061,74.061,0,0,0,318.541,348.3ZM320.093,464H48.186l.31-53.506a42.158,42.158,0,0,1,19.073-35.421l88.682-58.029L117.2,245.452A46.838,46.838,0,0,1,112.093,224V152a72,72,0,1,1,144,0v72a46.809,46.809,0,0,1-5.215,21.452L211.962,316.8l89.058,58.274a42.16,42.16,0,0,1,19.073,35.421Z" class="ci-primary"></path></svg>',
        declarations:
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<path fill="var(--ci-primary-color, currentColor)" d="M222.085,235.644l-62.01-62.01L81.8,251.905l62.009,62.01-.04.04,66.958,66.957,11.354,11.275.04.039,66.957-66.957,11.273-11.354L502.628,111.644,424.356,33.373Zm44.33,66.958-11.274,11.353h0l-33.057,33.056-.04-.039-33.017-33.017.04-.04-62.009-62.01,33.016-33.016,62.01,62.009L424.356,78.627l33.017,33.017Z" class="ci-primary"></path><polygon fill="var(--ci-primary-color, currentColor)" points="448 464 48 464 48 64 348.22 64 380.22 32 16 32 16 496 480 496 480 179.095 448 211.095 448 464" class="ci-primary"></polygon></svg>',
        "rat-result":
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<path fill="var(--ci-primary-color, currentColor)" d="M222.085,235.644l-62.01-62.01L81.8,251.905l62.009,62.01-.04.04,66.958,66.957,11.354,11.275.04.039,66.957-66.957,11.273-11.354L502.628,111.644,424.356,33.373Zm44.33,66.958-11.274,11.353h0l-33.057,33.056-.04-.039-33.017-33.017.04-.04-62.009-62.01,33.016-33.016,62.01,62.009L424.356,78.627l33.017,33.017Z" class="ci-primary"></path><polygon fill="var(--ci-primary-color, currentColor)" points="448 464 48 464 48 64 348.22 64 380.22 32 16 32 16 496 480 496 480 179.095 448 211.095 448 464" class="ci-primary"></polygon></svg>',
        messaging:
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<rect width="32" height="32" x="144" y="240" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="240" y="240" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="336" y="240" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><path fill="var(--ci-primary-color, currentColor)" d="M464,32H48A32.036,32.036,0,0,0,16,64V352a32.036,32.036,0,0,0,32,32h64V496h30.627l112-112H464a32.036,32.036,0,0,0,32-32V64A32.036,32.036,0,0,0,464,32Zm0,320H241.373L144,449.373V352H48V64H464Z" class="ci-primary"></path></svg>',
        "sms-templates":
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<rect width="256" height="32" x="128" y="192" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="128" height="32" x="128" y="304" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><path fill="var(--ci-primary-color, currentColor)" d="M48,432H464V88H48ZM80,120H432V400H80Z" class="ci-primary"></path></svg>',
        roles:
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<path fill="var(--ci-primary-color, currentColor)" d="M426.072,86.928A238.75,238.75,0,0,0,88.428,424.572,238.75,238.75,0,0,0,426.072,86.928ZM257.25,462.5c-114,0-206.75-92.748-206.75-206.75S143.248,49,257.25,49,464,141.748,464,255.75,371.252,462.5,257.25,462.5Z" class="ci-primary"></path><polygon fill="var(--ci-primary-color, currentColor)" points="221.27 305.808 147.857 232.396 125.23 255.023 221.27 351.063 388.77 183.564 366.142 160.937 221.27 305.808" class="ci-primary"></polygon></svg>',
        settings:
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<path fill="var(--ci-primary-color, currentColor)" d="M245.151,168a88,88,0,1,0,88,88A88.1,88.1,0,0,0,245.151,168Zm0,144a56,56,0,1,1,56-56A56.063,56.063,0,0,1,245.151,312Z" class="ci-primary"></path><path fill="var(--ci-primary-color, currentColor)" d="M464.7,322.319l-31.77-26.153a193.081,193.081,0,0,0,0-80.332l31.77-26.153a19.941,19.941,0,0,0,4.606-25.439l-32.612-56.483a19.936,19.936,0,0,0-24.337-8.73l-38.561,14.447a192.038,192.038,0,0,0-69.54-40.192L297.49,32.713A19.936,19.936,0,0,0,277.762,16H212.54a19.937,19.937,0,0,0-19.728,16.712L186.05,73.284a192.03,192.03,0,0,0-69.54,40.192L77.945,99.027a19.937,19.937,0,0,0-24.334,8.731L21,164.245a19.94,19.94,0,0,0,4.61,25.438l31.767,26.151a193.081,193.081,0,0,0,0,80.332l-31.77,26.153A19.942,19.942,0,0,0,21,347.758l32.612,56.483a19.937,19.937,0,0,0,24.337,8.73l38.562-14.447a192.03,192.03,0,0,0,69.54,40.192l6.762,40.571A19.937,19.937,0,0,0,212.54,496h65.222a19.936,19.936,0,0,0,19.728-16.712l6.763-40.572a192.038,192.038,0,0,0,69.54-40.192l38.564,14.449a19.938,19.938,0,0,0,24.334-8.731L469.3,347.755A19.939,19.939,0,0,0,464.7,322.319Zm-50.636,57.12-48.109-18.024-7.285,7.334a159.955,159.955,0,0,1-72.625,41.973l-10,2.636L267.6,464h-44.89l-8.442-50.642-10-2.636a159.955,159.955,0,0,1-72.625-41.973l-7.285-7.334L76.241,379.439,53.8,340.562l39.629-32.624-2.7-9.973a160.9,160.9,0,0,1,0-83.93l2.7-9.972L53.8,171.439l22.446-38.878,48.109,18.024,7.285-7.334a159.955,159.955,0,0,1,72.625-41.973l10-2.636L222.706,48H267.6l8.442,50.642,10,2.636a159.955,159.955,0,0,1,72.625,41.973l7.285,7.334,48.109-18.024,22.447,38.877-39.629,32.625,2.7,9.972a160.9,160.9,0,0,1,0,83.93l-2.7,9.973,39.629,32.623Z" class="ci-primary"></path></svg>',
        "qr-codes":
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<polygon fill="var(--ci-primary-color, currentColor)" points="48 48 176 48 176 16 16 16 16 176 48 176 48 48" class="ci-primary"></polygon><path fill="var(--ci-primary-color, currentColor)" d="M176,176V80H80v96h96Zm-64-64h32v32H112Z" class="ci-primary"></path><polygon fill="var(--ci-primary-color, currentColor)" points="328 48 464 48 464 176 496 176 496 16 328 16 328 48" class="ci-primary"></polygon><path fill="var(--ci-primary-color, currentColor)" d="M432,176V80H336v96h96Zm-64-64h32v32H368Z" class="ci-primary"></path><polygon fill="var(--ci-primary-color, currentColor)" points="176 464 48 464 48 336 16 336 16 496 176 496 176 464" class="ci-primary"></polygon><path fill="var(--ci-primary-color, currentColor)" d="M176,336H80v96h96V336Zm-32,64H112V368h32Z" class="ci-primary"></path><polygon fill="var(--ci-primary-color, currentColor)" points="464 464 328 464 328 496 496 496 496 336 464 336 464 464" class="ci-primary"></polygon><polygon fill="var(--ci-primary-color, currentColor)" points="272 304 400 304 400 368 432 368 432 272 272 272 272 304" class="ci-primary"></polygon><polygon fill="var(--ci-primary-color, currentColor)" points="432 432 432 400 240 400 240 272 80 272 80 304 208 304 208 432 432 432" class="ci-primary"></polygon><rect width="32" height="96" x="208" y="80" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><polygon fill="var(--ci-primary-color, currentColor)" points="80 240 304 240 304 80 272 80 272 208 80 208 80 240" class="ci-primary"></polygon><rect width="96" height="32" x="336" y="208" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="336" y="336" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="272" y="336" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect></svg>',
        "system-crons":
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<polygon fill="var(--ci-primary-color, currentColor)" points="271.514 95.5 239.514 95.5 239.514 273.611 355.127 328.559 368.864 299.657 271.514 253.389 271.514 95.5" class="ci-primary"></polygon><path fill="var(--ci-primary-color, currentColor)" d="M256,16C123.452,16,16,123.452,16,256S123.452,496,256,496,496,388.548,496,256,388.548,16,256,16Zm0,448C141.125,464,48,370.875,48,256S141.125,48,256,48s208,93.125,208,208S370.875,464,256,464Z" class="ci-primary"></path></svg>',
        "send-sms":
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<path fill="var(--ci-primary-color, currentColor)" d="M474.444,19.857a20.336,20.336,0,0,0-21.592-2.781L33.737,213.8v38.066l176.037,70.414L322.69,496h38.074l120.3-455.4A20.342,20.342,0,0,0,474.444,19.857ZM337.257,459.693,240.2,310.37,389.553,146.788l-23.631-21.576L215.4,290.069,70.257,232.012,443.7,56.72Z" class="ci-primary"></path></svg>',
        "sms-schedule":
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<rect width="32" height="32" x="240" y="384" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="96" y="240" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="384" y="240" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><path fill="var(--ci-primary-color, currentColor)" d="M414.392,97.608A222.332,222.332,0,0,0,272,32.567V32H240v96h32V64.672C370.41,72.83,448,155.519,448,256c0,105.869-86.131,192-192,192S64,361.869,64,256a191.61,191.61,0,0,1,56.408-135.942l115.624,145.88,25.078-19.876L124.6,73.828l-12.606,10.59a224,224,0,1,0,302.4,13.19Z" class="ci-primary"></path></svg>',
        "sms-status":
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<path fill="var(--ci-primary-color, currentColor)" d="M425.706,142.294A240,240,0,0,0,16,312v88H160V368H48V312c0-114.691,93.309-208,208-208s208,93.309,208,208v56H352v32H496V312A238.432,238.432,0,0,0,425.706,142.294Z" class="ci-primary"></path><rect width="32" height="32" x="80" y="264" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="240" y="128" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="136" y="168" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="32" height="32" x="400" y="264" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><path fill="var(--ci-primary-color, currentColor)" d="M297.222,335.1l69.2-144.173-28.85-13.848L268.389,321.214A64.141,64.141,0,1,0,297.222,335.1ZM256,416a32,32,0,1,1,32-32A32.036,32.036,0,0,1,256,416Z" class="ci-primary"></path></svg>',
        "system-log":
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<path fill="var(--ci-primary-color, currentColor)" d="M256.25,16A240,240,0,0,0,88,84.977V16H56V144H184V112H106.287A208,208,0,0,1,256.25,48C370.8,48,464,141.2,464,255.75S370.8,463.5,256.25,463.5,48.5,370.3,48.5,255.75h-32A239.75,239.75,0,0,0,425.779,425.279,239.75,239.75,0,0,0,256.25,16Z" class="ci-primary"></path><polygon fill="var(--ci-primary-color, currentColor)" points="240 111.951 239.465 288 368 288 368 256 271.563 256 272 112.049 240 111.951" class="ci-primary"></polygon></svg>',
        department:
          '<svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" viewBox="0 0 512 512" role="img">undefined<rect width="256" height="32" x="128" y="192" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><rect width="128" height="32" x="128" y="304" fill="var(--ci-primary-color, currentColor)" class="ci-primary"></rect><path fill="var(--ci-primary-color, currentColor)" d="M48,432H464V88H48ZM80,120H432V400H80Z" class="ci-primary"></path></svg>',
      },
    };
  },
  props: ["isOpen", "menuItems"],
  watch: {
    isOpen: {
      immediate: true,
      handler(isOpen) {
        if (process.client) {
          if (isOpen) document.body.style.setProperty("overflow", "hidden");
          else document.body.style.removeProperty("overflow");
        }
      },
    },
  },
  mounted() {
    document.addEventListener("keydown", (e) => {
      if (e.keyCode == 27 && this.isOpen) this.isOpen = false;
    });
  },
  methods: {
    toggleMenu(event) {
      console.log(event);
      event.target.parentElement.classList.toggle("show");
    },
  },
};
</script>
