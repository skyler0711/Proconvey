import classNames from 'classnames'
import React, { Children, cloneElement } from 'react'
import SidebarItem from './SidebarItem'

type PropTypes = {
  currentStep: number
  children: React.ReactNode
}

const StepSidebar = ({ currentStep, children }: PropTypes) => {
  return (
    <div className="relative transition-all max-w-[18.375rem] h-full overflow-y-auto bg-white py-[3.25rem] pt-[3.75rem] px-[23px] w-full">
      {
        Children.map(children, (child, index) => {
          if (React.isValidElement(child)) {
            return (
              <>
                {
                  index !== 0 &&
                  <div className={classNames('rounded-full relative ml-[0.8125rem] h-[60px] my-1.5 w-1', {
                    'bg-gainsboro': index > currentStep,
                    'bg-primary': index <= currentStep,
                    'bg-chalk': index === currentStep,
                  })}></div>
                }
                {
                  cloneElement(child, {
                    active: currentStep === index + 1,
                    completed: index + 1 < currentStep,
                    step: index + 1,
                  } as Partial<unknown>)
                }
              </>
            )
          }
        })
      }
    </div>
  )
}

StepSidebar.Item = SidebarItem

export default StepSidebar
