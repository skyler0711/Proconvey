import React from 'react'
import { FileIcon, HamburgerIcon, OverviewIcon, SettingsIcon, UserIcon } from '@proconvey/ui/src/icons'
import Logo from '@proconvey/ui/src/svgs/logo'
import Menu from '@proconvey/ui/src/components/Menu'
import SearchBar from '@proconvey/ui/src/components/Searchbar'
import Profile from '@proconvey/ui/src/components/Profile'
import NotificationsDropdown from '@proconvey/ui/src/components/NotificationsDropdown'
import Button from '@proconvey/ui/src/components/Button'
import { useRouter } from 'next/router'
import Link from 'next/link'
import useNotifications from 'hooks/useNotifications'
import useSearch from '../hooks/useSearch'
import useLogout from 'hooks/useLogout'
import useSidebar from 'hooks/useSidebar'
import ModalDrawer from '@proconvey/ui/src/components/ModalDrawer'
import IconButton from '@proconvey/ui/src/components/IconButton'

const MainContent = ({ children }: { children: React.ReactNode }) => (
  <div className="w-full bg-outlined py-[3.125rem] px-5 lg:pl-[1.875rem] lg:pr-[3.125rem] overflow-y-auto">
    <div className="max-w-[1450px] mx-auto">
      {children}
    </div>
  </div>
)

const ConveyancerPortalLayout = ({ children }: { children: React.ReactNode }) => {

  const router = useRouter()

  const { setSearch, fetchingGlobalSearch, results, noResults } = useSearch()

  const { user, handleLogout } = useLogout()

  const { markAllNotificationsRead, fetching: isFetchingNotifications } = useNotifications()

  const notifications = user.unread_notifications

  const { isSidebarOpen, setIsSidebarOpen } = useSidebar()

  const getMenuContent = (isMobile: boolean = false) => {
    return <>
      {!router.pathname.startsWith('/clients/[id]')
        ? <>
          <Link href="/clients" className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
            <Menu.Item icon={<UserIcon />} active={router.pathname.startsWith('/clients')}>
              Clients
            </Menu.Item>
          </Link>
          <Menu.Dropdown icon={<SettingsIcon />} text="Settings" defaultOpen={router.pathname.startsWith('/settings')}>
            <Link href="/settings/overview" className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
              <Menu.SubItem active={router.pathname.endsWith('/overview')}>Overview</Menu.SubItem>
            </Link>
            <Link href="/settings/profile" className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
              <Menu.SubItem active={router.pathname.endsWith('/profile')}>Profile Settings</Menu.SubItem>
            </Link>
            <Link href="/settings/business" className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
              <Menu.SubItem active={router.pathname.endsWith('/business')}>Business Settings</Menu.SubItem>
            </Link>
            <Link href="/settings/onboarding" className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
              <Menu.SubItem active={router.pathname.endsWith('/onboarding') || router.pathname.endsWith('/client-care-letter-sale') || router.pathname.endsWith('/client-care-letter-purchase') || router.pathname.endsWith('/terms-and-conditions') || router.pathname.endsWith('/letter-header-and-footer')}>Onboarding Settings</Menu.SubItem>
            </Link>
            <Link href="/settings/team-members" className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
              <Menu.SubItem active={router.pathname.endsWith('/team-members')}>Team Members</Menu.SubItem>
            </Link>
            <Link href="/settings/billing" className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
              <Menu.SubItem active={router.pathname.endsWith('/billing')}>Billing</Menu.SubItem>
            </Link>
            <Link href="/settings/notification-preferences" className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
              <Menu.SubItem active={router.pathname.endsWith('/notification-preferences')}>Notification Settings</Menu.SubItem>
            </Link>
          </Menu.Dropdown>
        </>
        : null
      }

      {router.pathname.startsWith('/clients/[id]')
        ? <>
          <Link href={`/clients/${router.query.id}`} className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
            <Menu.Item icon={<OverviewIcon />} active={router.pathname === '/clients/[id]'}>
              Overview
            </Menu.Item>
          </Link>

          <Link href={`/clients/${router.query.id}/pack`} className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
            <Menu.Item icon={<FileIcon />} active={router.pathname === '/clients/[id]/pack'}>
              Documents
            </Menu.Item>
          </Link>
        </>
        : null
      }
    </>
  }

  return (
    <div className="flex flex-col h-full">
      <div className="flex gap-5 justify-between items-center w-full px-5 lg:px-[3.125rem] pt-[1.375rem] pb-[1.125rem] border-b border-primary/15">
        <div className="flex w-full gap-5">
          <div className="lg:hidden">
            <IconButton icon={<HamburgerIcon />} onClick={() => setIsSidebarOpen(true)} />
          </div>
          <div className="max-w-[275px] w-full flex-shrink">
            <Link href="/clients">
              <Logo className="w-[135px]" />
            </Link>
          </div>
          <div className="max-w-[450px] hidden lg:block w-full">
            <SearchBar
              onChange={setSearch}
              results={results}
              fetching={fetchingGlobalSearch}
              noResults={noResults}
            />
          </div>
        </div>


        <div className="flex justify-end w-full gap-4 lg:gap-16">
          <Link className="hidden lg:block" href="/properties/invite">
            <Button wrap={false}>
              Invite new client
            </Button>
          </Link>

          <div className="flex gap-2 lg:gap-7">
            <NotificationsDropdown length={notifications?.length ?? 0} markAllNotificationsRead={markAllNotificationsRead} isLoading={isFetchingNotifications}>
              {
                notifications && notifications.map((notification) => (
                  <NotificationsDropdown.Item key={`notification-${notification?.id}`} notificationData={notification?.data ?? undefined} timestamp={notification?.created_at}>
                    {notification?.data?.message}
                  </NotificationsDropdown.Item>
                ))
              }
            </NotificationsDropdown>
            <Profile user={{ first_name: user.first_name ?? '', last_name: user.last_name ?? '', profile_image: user.profile_image }}>
              <Profile.Item onClick={handleLogout}>Logout</Profile.Item>
            </Profile>
          </div>
        </div>
      </div>

      <div className="flex flex-grow w-full min-h-0">
        <div className="flex-col max-w-[294px] w-full border-r border-primary/15 bg-white hidden lg:flex">
          <Menu>
            {getMenuContent()}
          </Menu>
        </div>

        <ModalDrawer isOpen={isSidebarOpen} setIsOpen={(isOpen: boolean) => setIsSidebarOpen(isOpen)}>
          <SearchBar
            onChange={setSearch}
            results={results}
            fetching={fetchingGlobalSearch}
            noResults={noResults}
            className="-mx-5"
            onClick={() => setIsSidebarOpen(false)}
          />

          {getMenuContent(true)}

          <Link href="/properties/invite" className="mt-auto" onClick={() => setIsSidebarOpen(false)}>
            <Button block wrap={false}>
              Invite new client
            </Button>
          </Link>
        </ModalDrawer>

        {children}
      </div>
    </div>
  )
}

ConveyancerPortalLayout.MainContent = MainContent

export default ConveyancerPortalLayout
