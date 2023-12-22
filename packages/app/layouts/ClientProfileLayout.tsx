import Logo from '@proconvey/ui/src/svgs/logo'
import { ReactNode } from 'react'

const MainContent = ({ children }: {children: ReactNode}) => (
  <div className="w-full min-h-screen bg-outlined py-[3.125rem] px-[1.875rem]">
    <div className="w-full max-w-[1450px] mx-auto">
      {children}
    </div>
  </div>
)

const ClientProfileLayout = ({ children }: {children: ReactNode}) => {
  return (
    <div>
      <div className="flex justify-between gap-5 w-full px-5 lg:px-[3.125rem] pt-[1.375rem] pb-[1.125rem]">
        <Logo className="w-[8.4375rem] h-[3.125rem]" />
      </div>
      <hr className="text-purple" />
      {children}
    </div>
  )
}

ClientProfileLayout.MainContent = MainContent

export default ClientProfileLayout
