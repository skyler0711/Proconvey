import { ReactNode } from 'react'

const LeftBlock = ({ children }: {children: ReactNode}) => (
  <div className="md:w-1/2 px-5 max-w-full mb-[3.125rem]">
    {children}
  </div>
)

const RightBlock = ({ children }: {children: ReactNode}) => (
  <div className="fixed inset-y-0 right-0 flex-col hidden md:flex md:w-1/2">
    {children}
  </div>
)

const AuthLayout = ({ children }: { children: ReactNode }) => {
  return (
    <div className="flex flex-col w-full md:flex-row md:h-screen">
      {children}
    </div>
  )
}

AuthLayout.Left = LeftBlock
AuthLayout.Right = RightBlock

export default AuthLayout
